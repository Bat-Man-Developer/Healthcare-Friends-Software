"""
Medical Diagnosis AI using IBM Granite Models
Specialized for virtual doctor consultations
"""

import sys
import json
import time
import logging
from datetime import datetime
import requests

# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler('c:/Xampp/htdocs/healthcarefriends-website/logs/healthcheckup.log'),
        logging.StreamHandler()
    ]
)

class MedicalDiagnosisAI:
    def __init__(self):
        # IBM Watson credentials - IMPORTANT: Use your actual credentials
        self.project_id = "bb2265fb-2e89-474b-a008-867cd95613c0"
        self.endpoint_url = "https://us-south.ml.cloud.ibm.com"
        self.api_key = "9C-lT-iAdYzCPPXMoClYy_YK1aD7DLAL9Ajoioaw6gy0"
        
        # Current IAM token - IMPORTANT: This needs to be refreshed regularly
        self.iam_token = "eyJraWQiOiIyMDE5MDcyNCIsImFsZyI6IlJTMjU2In0.eyJpYW1faWQiOiJJQk1pZC02OTgwMDBaOTA1IiwiaWQiOiJJQk1pZC02OTgwMDBaOTA1IiwicmVhbG1pZCI6IklCTWlkIiwic2Vzc2lvbl9pZCI6IkMtZjBmNzgyMWYtOGM4YS00MjU4LWE2MTYtYjU3ZDViNGE4MmJiIiwic2Vzc2lvbl9leHBfbWF4IjoxNzUxMjQzMzY5LCJzZXNzaW9uX2V4cF9uZXh0IjoxNzUxMTY0MjUyLCJqdGkiOiI5MzdlOTc1Yy1iNTcwLTRlYjAtYTA2MC00N2JjNjRhMDgwYzIiLCJpZGVudGlmaWVyIjoiNjk4MDAwWjkwNSIsImdpdmVuX25hbWUiOiJLYXkiLCJmYW1pbHlfbmFtZSI6Ik11ZGF1IiwibmFtZSI6IktheSBNdWRhdSIsImVtYWlsIjoia2theS5tdWRhdTAwOEBnbWFpbC5jb20iLCJzdWIiOiJra2F5Lm11ZGF1MDA4QGdtYWlsLmNvbSIsImF1dGhuIjp7InN1YiI6ImtrYXkubXVkYXUwMDhAZ21haWwuY29tIiwiaWFtX2lkIjoiSUJNaWQtNjk4MDAwWjkwNSIsIm5hbWUiOiJLYXkgTXVkYXUiLCJnaXZlbl9uYW1lIjoiS2F5IiwiZmFtaWx5X25hbWUiOiJNdWRhdSIsImVtYWlsIjoia2theS5tdWRhdTAwOEBnbWFpbC5jb20ifSwiYWNjb3VudCI6eyJ2YWxpZCI6dHJ1ZSwiYnNzIjoiMTE2NGNkMjlhMzY1NDJjN2EzYmUyOTI4YjYxZDI1NDciLCJpbXNfdXNlcl9pZCI6IjEzOTQ0Mzk3IiwiaW1zIjoiMzAwMDA4NCJ9LCJpYXQiOjE3NTExNTcwNDksImV4cCI6MTc1MTE1ODI0OSwiaXNzIjoiaHR0cHM6Ly9pYW0uY2xvdWQuaWJtLmNvbS9pZGVudGl0eSIsImdyYW50X3R5cGUiOiJ1cm46aWJtOnBhcmFtczpvYXV0aDpncmFudC10eXBlOnBhc3Njb2RlIiwic2NvcGUiOiJpYm0gb3BlbmlkIiwiY2xpZW50X2lkIjoiYngiLCJhY3IiOjEsImFtciI6WyJwd2QiXX0.K4gLqf1yya-rGr-8TL0Wzt11In8Ux-RGFgbK9QhClkOp3YpiDp9FjYDmDNfXRAxtVVDHyXuLVbJZKEgAn_6lCBRhMCl4d5IFJUTgPSvRu6IoyoAAhrNzByAFbOa0D6NRDw89MZRBYm0PZQG37mlif8xlBpyJ8cGuoC_dO6ePF0Wv_OgQqg7IYktzAk08RphNzOhOruhkW5dxy1ljCjb2yz5Fc60G42B1yLzxCMOUwBbMw0sVxRsqBVH6KLbPXBiei62QhS8mU2LCT-QjQg9BrNaZHQHGvhoUbatoSKdpNlLq5HnqGPcHGIjgSTAtfPxv8XaIu8q2IUBzrzZ8zKK_5w"
        
        # Medical model configurations
        self.models = {
            'granite33': {
                'model_id': 'ibm/granite-3-3-8b-instruct',
                'max_tokens': 2000,
                'temperature': 0.3  # Lower temperature for medical accuracy
            },
            'granite40': {
                'model_id': 'ibm/granite-4-0-tiny',
                'max_tokens': 1500,
                'temperature': 0.2  # Even lower for medical precision
            }
        }
        
        self.access_token = None
        self.token_expires = 0

    def get_access_token(self):
        """Get IBM Cloud access token"""
        try:
            if self.access_token and time.time() < self.token_expires:
                return self.access_token

            url = "https://iam.cloud.ibm.com/identity/token"
            headers = {
                "Content-Type": "application/x-www-form-urlencoded"
            }
            data = {
                "grant_type": "urn:iam:params:oauth:grant-type:apikey",
                "apikey": self.api_key
            }

            response = requests.post(url, headers=headers, data=data, timeout=30)
            response.raise_for_status()

            token_data = response.json()
            self.access_token = token_data['access_token']
            self.token_expires = time.time() + token_data.get('expires_in', 3600) - 300

            logging.info("Successfully obtained access token for medical diagnosis")
            return self.access_token

        except Exception as e:
            logging.error(f"Failed to get access token: {str(e)}")
            # Fall back to the provided IAM token
            return self.iam_token

    def generate_medical_assessment(self, model_type, prompt):
        """Generate medical assessment using IBM Granite models"""
        try:
            if model_type not in self.models:
                raise ValueError(f"Invalid model type: {model_type}")

            start_time = time.time()
            
            # Get access token (or use fallback)
            access_token = self.get_access_token()
            
            # Prepare request
            model_config = self.models[model_type]
            url = f"{self.endpoint_url}/ml/v1/text/generation?version=2023-05-29"
            
            headers = {
                "Authorization": f"Bearer {access_token}",
                "Content-Type": "application/json",
                "Accept": "application/json"
            }
            
            # Enhanced medical prompt
            medical_prompt = self.create_medical_prompt(prompt)
            
            payload = {
                "input": medical_prompt,
                "parameters": {
                    "decoding_method": "greedy",
                    "max_new_tokens": model_config['max_tokens'],
                    "temperature": model_config['temperature'],
                    "repetition_penalty": 1.1,
                    "stop_sequences": ["</assessment>", "\n\nPatient:", "\n\nDoctor:"]
                },
                "model_id": model_config['model_id'],
                "project_id": self.project_id
            }

            logging.info(f"Generating medical assessment with {model_type}")
            
            # Make request
            response = requests.post(url, headers=headers, json=payload, timeout=120)
            response.raise_for_status()

            result = response.json()
            
            # Extract generated text
            if 'results' in result and len(result['results']) > 0:
                generated_text = result['results'][0]['generated_text'].strip()
                
                # Process medical response
                processed_response = self.process_medical_response(generated_text)
                
                processing_time = time.time() - start_time
                
                logging.info(f"Successfully generated medical assessment in {processing_time:.2f}s")
                
                return {
                    'response': processed_response,
                    'processing_time': processing_time,
                    'model_used': model_config['model_id'],
                    'timestamp': datetime.now().isoformat(),
                    'confidence_level': self.assess_confidence(generated_text)
                }
            else:
                raise Exception("No results returned from medical model")

        except requests.exceptions.RequestException as e:
            logging.error(f"Request error in medical assessment: {str(e)}")
            raise Exception(f"Medical API request failed: {str(e)}")
        except Exception as e:
            logging.error(f"Medical assessment error: {str(e)}")
            raise Exception(f"Medical assessment failed: {str(e)}")

    def create_medical_prompt(self, user_prompt):
        """Create specialized medical assessment prompt"""
        medical_context = """You are Dr. AI Assistant, an experienced medical AI providing health assessments. You have extensive knowledge of medical conditions, symptoms, and healthcare protocols.

IMPORTANT GUIDELINES:
1. Provide accurate, evidence-based medical information
2. Always emphasize the need for professional medical consultation
3. Use clear, patient-friendly language
4. Include urgency levels and safety recommendations
5. Structure your response clearly with numbered sections
6. Never provide specific drug dosages or prescriptions
7. Always include appropriate disclaimers

Your assessment should include:
1. Possible Conditions: List potential diagnoses with brief explanations
2. Urgency Level: LOW/MODERATE/HIGH with clear reasoning
3. Immediate Actions: What the patient should do now
4. When to Seek Care: Specific scenarios requiring immediate/urgent care
5. General Recommendations: Lifestyle advice and monitoring guidance
6. Red Flags: Warning signs that require immediate medical attention

Remember: This is an educational assessment tool, not a replacement for professional medical care.

"""
        
        return f"{medical_context}\nPatient Information and Symptoms:\n{user_prompt}\n\nMedical Assessment:"

    def process_medical_response(self, response):
        """Process and format medical response for patient safety"""
        # Add safety header
        safety_header = "🏥 VIRTUAL MEDICAL ASSESSMENT\n" + "="*50 + "\n\n"
        
        # Add important disclaimer at the beginning
        disclaimer = """⚠️ IMPORTANT MEDICAL DISCLAIMER ⚠️
This assessment is provided by an AI system for educational purposes only. 
It is NOT a substitute for professional medical advice, diagnosis, or treatment.
Always consult with a qualified healthcare provider for medical concerns.

In case of emergency, call 112 immediately.
\n"""
        
        # Format the response
        formatted_response = safety_header + disclaimer + response
        
        # Add timestamp and confidence note
        timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        footer = f"\n\n📅 Assessment generated: {timestamp}"
        footer += "\n\n🔍 Next Steps: Schedule an appointment with your healthcare provider to discuss these findings."
        
        return formatted_response + footer

    def assess_confidence(self, response):
        """Assess confidence level of the medical response"""
        confidence_indicators = [
            "likely", "probable", "possible", "may indicate", 
            "suggests", "consistent with", "typical of"
        ]
        
        uncertainty_indicators = [
            "unclear", "uncertain", "difficult to determine",
            "requires further evaluation", "needs investigation"
        ]
        
        response_lower = response.lower()
        
        confidence_score = 0
        for indicator in confidence_indicators:
            if indicator in response_lower:
                confidence_score += 1
                
        for indicator in uncertainty_indicators:
            if indicator in response_lower:
                confidence_score -= 1
        
        # Normalize to percentage
        confidence_percentage = max(10, min(90, 60 + (confidence_score * 10)))
        
        return f"{confidence_percentage}%"

def main():
    """Main function for medical diagnosis"""
    try:
        if len(sys.argv) != 3:
            raise ValueError("Usage: python medical_diagnosis.py <model_type> <prompt_file>")
        
        model_type = sys.argv[1].strip()
        prompt_file = sys.argv[2].strip()
        
        # Load prompt from file
        try:
            with open(prompt_file, 'r', encoding='utf-8') as f:
                prompt = f.read().strip()
        except Exception as e:
            raise Exception(f"Failed to load prompt file: {str(e)}")
        
        # Validate inputs
        if not model_type or not prompt:
            raise ValueError("Model type and prompt are required")
        
        # Initialize medical AI
        medical_ai = MedicalDiagnosisAI()
        
        # Generate assessment
        result = medical_ai.generate_medical_assessment(model_type, prompt)
        
        # Return JSON response
        print(json.dumps({
            'success': True,
            'response': result['response'],
            'processing_time': result['processing_time'],
            'model_used': result['model_used'],
            'timestamp': result['timestamp'],
            'confidence_level': result['confidence_level']
        }))

    except Exception as e:
        logging.error(f"Medical diagnosis execution error: {str(e)}")
        print(json.dumps({
            'success': False,
            'error': str(e),
            'timestamp': datetime.now().isoformat()
        }))
        sys.exit(1)

if __name__ == "__main__":
    main()