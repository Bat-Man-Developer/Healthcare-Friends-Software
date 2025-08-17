Healthcare Friends - Virtual Health Consultant

[PHP](https://www.php.net/)
[JavaScript](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[Python](https://www.python.org/)
[IBM watsonx](https://www.ibm.com/watsonx)
[Three.js](https://threejs.org/)
[License](LICENSE)

A sophisticated virtual health consultation platform powered by IBM watsonx Granite models and agentic AI technology. Healthcare Friends provides free, privacy-focused preliminary health assessments through an interactive 3D doctor avatar interface.

🚀 Features

Core Functionality

•	🏥 Virtual Health Consultations: Comprehensive 5-step health assessment process

•	🤖 3D Doctor Avatar: Interactive Three.js-powered virtual doctor with realistic animations

•	🗣️ Voice Interaction: Speech recognition and text-to-speech capabilities

•	🔒 Privacy-First: Zero personal data collection with session-based security

•	📱 Responsive Design: Mobile-optimized interface for all devices

•	🌐 Multi-language Support: Extensible internationalization framework

AI-Powered Features

•	🧠 Agentic AI System: Autonomous conversation management and diagnostic reasoning

•	⚡ IBM watsonx Integration: Granite-3.3-8b-instruct model for medical analysis

•	📊 Smart Symptom Analysis: Context-aware health assessment algorithms

•	🎯 Personalized Recommendations: Tailored health guidance based on user input

•	⚠️ Risk Assessment: Intelligent urgency level detection and triage

Technical Features

•	🔐 Enhanced Security: CSRF protection, XSS prevention, and secure headers

•	🎨 Modern UI/UX: Glassmorphism design with floating particle effects

•	📈 Real-time Processing: Asynchronous AI model calls with progress indicators

•	🔧 Admin Configuration: Dynamic IBM watsonx model configuration interface

•	📝 Comprehensive Logging: Detailed error tracking and performance monitoring

🏗️ Architecture

System Components

Healthcare Friends/

├── 📁 assets/                    # Static assets (CSS, images, icons)

├── 📁 database/              # Database file for importing

├── 📁 call_python/              # Python integration layer

│   └── 📁 ibm_granite_models/   # IBM watsonx Granite API handlers

├── 📁 config/                   # Configuration files

├── 📁 js/                       # Frontend JavaScript modules

├── 📁 layouts/                  # PHP layout components

├── 📁 logs/                     # Application logs

├── 📁 python/                   # Python AI model scripts

├── 📁 server/                   # Backend PHP scripts

├── 🔧 configurations.php        # Admin configuration interface

├── 🏥 healthcheckup.php                 # Main consultation interface

└── 📚 README.md                 # Project documentation

AI Architecture
graph TD
    A[User Input] --> B[Virtual Doctor Class]
    B --> C[Conversation State Manager]
    C --> D[IBM watsonx Granite API]
    D --> E[Granite 3.3 Model]
    E --> G[Medical Analysis]
    F --> H[Response Validation]
    G --> I[Health Assessment]
    H --> I
    I --> J[3D Avatar Response]
    J --> K[User Interface]

🚀 Installation
Prerequisites
•	PHP 8.0+ with extensions: curl, json, session, mysqli
•	Python 3.8+ with packages: requests, json
•	Web Server: Apache/Nginx with mod_rewrite enabled
•	IBM watsonx Account: Active subscription with Granite model access
•	Modern Web Browser: Chrome, Firefox, Safari, or Edge with WebGL support

Quick Setup
1.	Clone the Repository
2.	git clone https://github.com/yourusername/healthcare-friends.git
3.	cd healthcare-friends
4.	Configure Web Server
5.	# Apache .htaccess configuration
6.	RewriteEngine On
7.	RewriteCond %{REQUEST_FILENAME} !-f
8.	RewriteCond %{REQUEST_FILENAME} !-d
9.	RewriteRule ^(.*)$ index.php [QSA,L]
10.	Install Python Dependencies
11.	pip install requests
12.	Database Setup
13.	CREATE DATABASE healthcare_friends;
14.	CREATE TABLE configurations (
15.	    id INT AUTO_INCREMENT PRIMARY KEY,
16.	    config_key VARCHAR(255) UNIQUE NOT NULL,
17.	    config_value TEXT,
18.	    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
19.	    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
20.	);
21.	Configure IBM watsonx

o	Navigate to /configurations.php
o	Enter your IBM watsonx credentials: 
	Project ID
	Endpoint URL
	API Key or IAM Token
	Model identifiers

22.	Set File Permissions
23.	chmod 755 python/
24.	chmod 644 config/
25.	chmod 666 logs/

⚙️ Configuration
IBM watsonx Setup
1.	Access Configuration Panel
2.	https://healthcarefriends.fcsholdix.co.za/configurations.php
3.	Required IBM watsonx Parameters
4.	{
5.	  "project_id": "your-watson-project-id",
6.	  "endpoint_url": "https://us-south.ml.cloud.ibm.com",
7.	  "api_key": "your-ibm-api-key",
8.	  "granite33_model": "ibm/granite-3-3-8b-instruct",
9.	  "granite40_model": "ibm/granite-4-0-tiny",
10.	  "iam_token": "optional-pre-generated-token"
11.	}
12.	Test Connection

o	Use the "Test Connection" button in the configuration panel
o	Verify Granite model accessibility
o	Check response times and error handling

Security Configuration
// Enhanced session security
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

// Security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");

🎯 Usage
Basic Health Consultation
1.	Access the Platform
2.	https://healthcarefriends.fcsholdix.co.za/
3.	Start Consultation

o	The 3D doctor avatar will greet you
o	Follow the 5-step assessment process: 
	Step 1: Age range selection
	Step 2: Biological sex specification
	Step 3: Main symptom description
	Step 4: Medical history and duration
	Step 5: Severity assessment

4.	Voice Interaction
5.	// Enable voice input
6.	document.getElementById('voiceBtn').click();
7.	// Speak your response
8.	// The system will automatically process and respond
9.	Receive Assessment

o	AI-generated health assessment
o	Risk level evaluation
o	Recommended actions
o	Professional care guidance

API Integration
// Call IBM Granite model directly
const response = await fetch('call_python/ibm_granite_models/getIBMGraniteModels.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        model_type: 'granite33',
        prompt: 'Patient symptoms and medical history...'
    })
});

const result = await response.json();

🤝 Contributing
We welcome contributions to Healthcare Friends! Please follow these guidelines:
Development Setup
1.	Fork the Repository
2.	Create Feature Branch 
3.	git checkout -b feature/your-feature-name
4.	Follow Coding Standards

o	PHP: PSR-12 coding standard
o	JavaScript: ES6+ with JSDoc comments
o	Python: PEP 8 style guide

Contribution Areas
•	🩺 Medical Knowledge Base: Enhance diagnostic algorithms
•	🌐 Internationalization: Add language support
•	🎨 UI/UX Improvements: Design enhancements
•	🔧 Performance Optimization: Speed and efficiency improvements
•	🧪 Testing: Unit and integration tests
•	📱 Mobile Features: iOS/Android native features

Pull Request Process
1.	Ensure all tests pass
2.	Update documentation
3.	Add changelog entry
4.	Request review from maintainers

📄 License
This project is licensed under the MIT License - see the LICENSE file for details.

🆘 Support
Documentation
•	📖 API Documentation
•	🔧 Configuration Guide
•	🚀 Deployment Guide

Community Support
•	💬 Discord Community
•	📧 Support Email
•	🐛 Bug Reports
•	💡 Feature Requests

Professional Support
For enterprise deployments and professional support:
•	🏢 Enterprise Solutions
•	🔧 Custom Development

🙏 Acknowledgments
•	IBM watsonx Team: For providing advanced AI models and platform
•	Three.js Community: For 3D web graphics framework
•	Medical Professionals: For consultation validation and guidance
•	Open Source Contributors: For continuous improvements and feedback

⚖️ Medical Disclaimer
Important: Healthcare Friends is an AI-powered health assessment tool designed for informational purposes only. It is not intended to replace professional medical advice, diagnosis, or treatment. Always seek the advice of your physician or other qualified healthcare provider with any questions you may have regarding a medical condition. Never disregard professional medical advice or delay seeking it because of something you have read or received from this application.
In case of a medical emergency, immediately call your local emergency services (911 in the US, 112 in Europe, etc.).
________________________________________
Made with ❤️ for global healthcare accessibility
Version: 1.0.0
Last Updated: August 2025
Powered by: IBM watsonx Granite Models
