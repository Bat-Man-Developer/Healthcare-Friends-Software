import sys
import json
import requests
from datetime import datetime
import os
import time

class IBMGraniteModels:
    def __init__(self, config):
        print(f"[DEBUG] Loaded configuration: {json.dumps({k: ('***' if 'token' in k.lower() or 'key' in k.lower() else v) for k, v in config.items()}, indent=2)}", file=sys.stderr)
        
        self.project_id = config.get('project_id', '')
        self.endpoint_url = config.get('endpoint_url', 'https://us-south.ml.cloud.ibm.com')
        self.api_key = config.get('api_key', '')
        self.iam_token = config.get('iam_token', '')
        
        self.models = {
            'granite33': config.get('granite33_model', 'ibm/granite-3-3-8b-instruct'),
            'granite40': config.get('granite40_model', 'ibm/granite-4-0-tiny')
        }
        
        print(f"[DEBUG] API Key present: {'Yes' if self.api_key else 'No'}", file=sys.stderr)
        print(f"[DEBUG] IAM Token present: {'Yes' if self.iam_token else 'No'}", file=sys.stderr)
        print(f"[DEBUG] Project ID: {self.project_id}", file=sys.stderr)
        print(f"[DEBUG] Endpoint URL: {self.endpoint_url}", file=sys.stderr)

    def get_iam_token(self):
        """Get IAM token using API key if not provided"""
        if self.iam_token:
            print("[DEBUG] Using pre-configured IAM token", file=sys.stderr)
            return self.iam_token
            
        if not self.api_key:
            raise Exception("Either IAM token or API key must be provided")
            
        print("[DEBUG] Generating IAM token from API key", file=sys.stderr)
        
        url = "https://iam.cloud.ibm.com/identity/token"
        headers = {
            "Content-Type": "application/x-www-form-urlencoded"
        }
        data = {
            "grant_type": "urn:iam:params:oauth:grant-type:apikey",
            "apikey": self.api_key
        }
        
        try:
            response = requests.post(url, headers=headers, data=data, timeout=30)
            if response.status_code == 200:
                token_response = response.json()
                access_token = token_response.get('access_token')
                print("[DEBUG] Successfully generated IAM token", file=sys.stderr)
                return access_token
            else:
                raise Exception(f"Failed to get IAM token: {response.status_code} - {response.text}")
        except Exception as e:
            raise Exception(f"Error generating IAM token: {str(e)}")

    def call_model(self, model_type, prompt):
        """Call IBM Granite model"""
        start_time = time.time()
        
        try:
            if model_type not in self.models:
                return {"error": f"Invalid model type: {model_type}"}
            
            # Validate configuration
            if not self.project_id:
                return {"error": "Project ID not configured"}
            
            print(f"[DEBUG] Calling model {model_type} with prompt length: {len(prompt)}", file=sys.stderr)
            
            # Get IAM token
            try:
                access_token = self.get_iam_token()
            except Exception as e:
                return {"error": f"Authentication failed: {str(e)}"}
            
            # Truncate prompt if too long for the model
            max_prompt_length = 8000  # Conservative limit
            if len(prompt) > max_prompt_length:
                print(f"[DEBUG] Truncating prompt from {len(prompt)} to {max_prompt_length} characters", file=sys.stderr)
                prompt = prompt[:max_prompt_length] + "\n\n[Note: Prompt truncated due to length limit]"
            
            # Prepare the request
            url = f"{self.endpoint_url}/ml/v1/text/generation?version=2023-05-29"
            
            headers = {
                "Accept": "application/json",
                "Content-Type": "application/json",
                "Authorization": f"Bearer {access_token}"
            }
            
            # Enhanced parameters for medical diagnosis
            body = {
                "input": prompt,
                "parameters": {
                    "decoding_method": "greedy",
                    "max_new_tokens": 1500,
                    "repetition_penalty": 1.1,
                    "temperature": 0.7,
                    "top_k": 50,
                    "top_p": 0.9
                },
                "model_id": self.models[model_type],
                "project_id": self.project_id
            }
            
            print(f"[DEBUG] Making API request to: {url}", file=sys.stderr)
            print(f"[DEBUG] Using model: {self.models[model_type]}", file=sys.stderr)
            
            # Make the request with retries
            max_retries = 3
            for attempt in range(max_retries):
                try:
                    response = requests.post(url, headers=headers, json=body, timeout=120)
                    break
                except requests.exceptions.Timeout:
                    if attempt == max_retries - 1:
                        return {"error": "Request timeout - model took too long to respond"}
                    print(f"[DEBUG] Timeout on attempt {attempt + 1}, retrying...", file=sys.stderr)
                    time.sleep(2)
                except requests.exceptions.ConnectionError:
                    if attempt == max_retries - 1:
                        return {"error": "Connection error - unable to reach IBM Watson"}
                    print(f"[DEBUG] Connection error on attempt {attempt + 1}, retrying...", file=sys.stderr)
                    time.sleep(2)
            
            print(f"[DEBUG] API response status: {response.status_code}", file=sys.stderr)
            
            processing_time = round(time.time() - start_time, 2)
            
            if response.status_code == 200:
                result = response.json()
                if "results" in result and len(result["results"]) > 0:
                    generated_text = result["results"][0]["generated_text"]
                    print(f"[DEBUG] Generated response length: {len(generated_text)}", file=sys.stderr)
                    return {
                        "response": generated_text,
                        "processing_time": processing_time
                    }
                else:
                    return {"error": "No response generated from model"}
            else:
                error_text = response.text
                print(f"[DEBUG] API error response: {error_text}", file=sys.stderr)
                
                # Parse error response for better error messages
                try:
                    error_json = response.json()
                    if 'errors' in error_json and len(error_json['errors']) > 0:
                        error_message = error_json['errors'][0].get('message', 'Unknown API error')
                    else:
                        error_message = error_json.get('message', error_text)
                except:
                    error_message = error_text
                
                # Specific error handling
                if response.status_code == 401:
                    error_message = "Authentication failed. Please check your API key or IAM token."
                elif response.status_code == 403:
                    error_message = "Access forbidden. Please check your project ID and permissions."
                elif response.status_code == 429:
                    error_message = "Rate limit exceeded. Please wait before making another request."
                elif response.status_code == 404:
                    error_message = "Model not found. Please check your model configuration."
                
                return {"error": f"API call failed with status {response.status_code}: {error_message}"}
                
        except Exception as e:
            print(f"[DEBUG] Unexpected exception: {str(e)}", file=sys.stderr)
            return {"error": f"Unexpected error: {str(e)}"}

def main():
    if len(sys.argv) != 4:
        print(json.dumps({"error": "Usage: python script.py <model_type> <prompt_file> <config_file>"}))
        return

    model_type = sys.argv[1]
    prompt_file = sys.argv[2]
    config_file = sys.argv[3]

    print(f"[DEBUG] Script started with model_type: {model_type}", file=sys.stderr)
    print(f"[DEBUG] Prompt file: {prompt_file}", file=sys.stderr)
    print(f"[DEBUG] Config file: {config_file}", file=sys.stderr)

    try:
        # Load configuration from file
        with open(config_file, 'r', encoding='utf-8') as f:
            config = json.load(f)
            
        print(f"[DEBUG] Configuration loaded successfully", file=sys.stderr)
    except Exception as e:
        print(json.dumps({"error": f"Failed to load configuration: {str(e)}"}))
        return

    try:
        # Load prompt from file
        with open(prompt_file, 'r', encoding='utf-8') as f:
            prompt = f.read()
            
        print(f"[DEBUG] Prompt loaded successfully, length: {len(prompt)}", file=sys.stderr)
    except Exception as e:
        print(json.dumps({"error": f"Failed to load prompt: {str(e)}"}))
        return

    granite_models = IBMGraniteModels(config)
    result = granite_models.call_model(model_type, prompt)

    print(json.dumps(result))

if __name__ == "__main__":
    main()