// Doctor Avatar 3D JS
// Ultra-Realistic 3D Doctor Avatar with Advanced Features
class UltraRealisticDoctorAvatar3D {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.scene = null;
        this.camera = null;
        this.renderer = null;
        this.doctorModel = null;
        this.mixer = null;
        this.clock = new THREE.Clock();
        this.currentExpression = 'idle';
        this.animationActions = {};
        this.blinkTimer = 0;
        this.emotionParticles = [];
        this.voiceActivity = false;
        this.isThinking = false;
        
        // Ultra-realistic features
        this.facialExpressions = {
            eyebrowRaise: 0,
            smileIntensity: 0,
            eyeOpenness: 1,
            headTilt: 0,
            jawOpenness: 0,
            cheekRaise: 0
        };
        
        // Skin material properties
        this.skinMaterialProperties = {
            subsurfaceScattering: true,
            roughness: 0.6,
            metalness: 0.0,
            clearcoat: 0.1
        };
        
        this.init();
    }

    init() {
        this.setupScene();
        this.setupCamera();
        this.setupRenderer();
        this.setupAdvancedLights();
        this.createUltraRealisticDoctorModel();
        this.setupAdvancedAnimations();
        this.setupInteractivity();
        this.createEnhancedUIElements();
        this.animate();
        
        window.addEventListener('resize', () => this.onWindowResize());
    }

    setupScene() {
        this.scene = new THREE.Scene();
        
        // Professional medical environment background
        const canvas = document.createElement('canvas');
        canvas.width = 1024;
        canvas.height = 1024;
        const context = canvas.getContext('2d');
        
        // Create gradient background with medical theme
        const gradient = context.createRadialGradient(512, 400, 0, 512, 400, 800);
        gradient.addColorStop(0, '#ffffff');
        gradient.addColorStop(0.3, '#f8fbff');
        gradient.addColorStop(0.6, '#e8f4fd');
        gradient.addColorStop(1, '#d6edfa');
        
        context.fillStyle = gradient;
        context.fillRect(0, 0, 1024, 1024);
        
        // Add subtle medical cross pattern
        context.globalAlpha = 0.03;
        context.fillStyle = '#4a90e2';
        for (let i = 0; i < 20; i++) {
            const x = Math.random() * 1024;
            const y = Math.random() * 1024;
            context.fillRect(x - 2, y - 8, 4, 16);
            context.fillRect(x - 8, y - 2, 16, 4);
        }
        
        const texture = new THREE.CanvasTexture(canvas);
        this.scene.background = texture;
        
        // Professional fog
        this.scene.fog = new THREE.Fog(0xf8fbff, 6, 20);
    }

    setupCamera() {
        this.camera = new THREE.PerspectiveCamera(
            45, 
            this.container.offsetWidth / this.container.offsetHeight, 
            0.1, 
            100
        );
        this.camera.position.set(0, 1.7, 3.5);
        this.camera.lookAt(0, 1.6, 0);
    }

    setupRenderer() {
        this.renderer = new THREE.WebGLRenderer({ 
            antialias: true, 
            alpha: true,
            powerPreference: "high-performance"
        });
        this.renderer.setSize(this.container.offsetWidth, this.container.offsetHeight);
        this.renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        this.renderer.shadowMap.enabled = true;
        this.renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        this.renderer.outputEncoding = THREE.sRGBEncoding;
        this.renderer.toneMapping = THREE.ACESFilmicToneMapping;
        this.renderer.toneMappingExposure = 1.3;
        this.renderer.physicallyCorrectLights = true;
        this.container.appendChild(this.renderer.domElement);
    }

    setupAdvancedLights() {
        // Professional studio lighting setup
        
        // Key light (main illumination)
        const keyLight = new THREE.DirectionalLight(0xffffff, 1.2);
        keyLight.position.set(3, 5, 4);
        keyLight.castShadow = true;
        keyLight.shadow.mapSize.setScalar(2048);
        keyLight.shadow.camera.near = 0.1;
        keyLight.shadow.camera.far = 15;
        keyLight.shadow.camera.left = -4;
        keyLight.shadow.camera.right = 4;
        keyLight.shadow.camera.top = 4;
        keyLight.shadow.camera.bottom = -4;
        keyLight.shadow.bias = -0.0001;
        keyLight.shadow.normalBias = 0.02;
        this.scene.add(keyLight);

        // Fill light (soft shadows)
        const fillLight = new THREE.DirectionalLight(0xb3d9ff, 0.4);
        fillLight.position.set(-3, 3, 3);
        this.scene.add(fillLight);

        // Rim light (professional edge lighting)
        const rimLight = new THREE.DirectionalLight(0xffffff, 0.6);
        rimLight.position.set(-2, 2, -3);
        this.scene.add(rimLight);

        // Eye light (catch light in eyes)
        const eyeLight = new THREE.PointLight(0xffffff, 0.2, 3);
        eyeLight.position.set(0, 1.8, 2);
        this.scene.add(eyeLight);

        // Ambient light with color temperature
        const ambientLight = new THREE.AmbientLight(0xf0f8ff, 0.3);
        this.scene.add(ambientLight);

        // Professional environment lighting
        const envLight1 = new THREE.PointLight(0xffeecc, 0.3, 8);
        envLight1.position.set(2, 3, 2);
        this.scene.add(envLight1);

        const envLight2 = new THREE.PointLight(0xe6f3ff, 0.25, 6);
        envLight2.position.set(-2, 2, 1);
        this.scene.add(envLight2);
    }

    createUltraRealisticDoctorModel() {
        this.doctorModel = new THREE.Group();
        
        this.createPhotorealisticHead();
        this.createProfessionalBody();
        this.createAnatomicallyCorrectArms();
        this.createDetailedAccessories();
        this.createMedicalEnvironment();
        
        this.scene.add(this.doctorModel);
    }

    createPhotorealisticHead() {
        // Advanced head geometry with proper proportions
        const headGeometry = new THREE.SphereGeometry(0.26, 64, 64);
        
        // Custom head shape modifications for more realistic proportions
        const headVertices = headGeometry.attributes.position.array;
        for (let i = 0; i < headVertices.length; i += 3) {
            const x = headVertices[i];
            const y = headVertices[i + 1];
            const z = headVertices[i + 2];
            
            // Create more realistic head shape with proper jaw and forehead
            const jawFactor = Math.max(0, -y + 0.05) * 0.4;
            const foreheadFactor = Math.max(0, y - 0.1) * 0.15;
            const chinFactor = y < -0.2 ? Math.abs(y + 0.2) * 0.8 : 0;
            
            headVertices[i] *= (1 - jawFactor + foreheadFactor - chinFactor * 0.3); // X scaling
            headVertices[i + 1] *= 1.08; // Y scaling (proper head height)
            headVertices[i + 2] *= (0.92 + jawFactor * 0.15 - chinFactor * 0.2); // Z scaling
        }
        
        headGeometry.attributes.position.needsUpdate = true;
        headGeometry.computeVertexNormals();
        
        // Enhanced skin material with realistic subsurface scattering effect
        const headMaterial = new THREE.MeshPhysicalMaterial({
            color: 0xfdbcb4,
            roughness: 0.8,
            metalness: 0.0,
            clearcoat: 0.03,
            clearcoatRoughness: 0.9,
            transmission: 0.005,
            ior: 1.4,
            opacity: 0.98,
            transparent: true
        });
        
        // Add skin texture for more realism
        this.createSkinTexture(headMaterial);
        
        this.head = new THREE.Mesh(headGeometry, headMaterial);
        this.head.position.set(0, 1.65, 0);
        this.head.castShadow = true;
        this.head.receiveShadow = true;
        this.head.name = 'head';
        
        this.createUltraRealisticEyes();
        this.createDetailedMouth();
        this.createRealisticNose();
        this.createExpressiveEyebrows();
        this.createProfessionalHairstyle();
        this.createDetailedEars();
        this.createFacialDetails();
        
        this.doctorModel.add(this.head);
    }

    createSkinTexture(material) {
        // Create procedural skin texture
        const canvas = document.createElement('canvas');
        canvas.width = 256;
        canvas.height = 256;
        const ctx = canvas.getContext('2d');
        
        // Base skin color
        ctx.fillStyle = '#fdbcb4';
        ctx.fillRect(0, 0, 256, 256);
        
        // Add skin pores and texture
        ctx.globalAlpha = 0.1;
        for (let i = 0; i < 3000; i++) {
            const x = Math.random() * 256;
            const y = Math.random() * 256;
            const size = Math.random() * 1.5;
            
            ctx.fillStyle = Math.random() > 0.5 ? '#e8a690' : '#f2c2a8';
            ctx.beginPath();
            ctx.arc(x, y, size, 0, Math.PI * 2);
            ctx.fill();
        }
        
        // Add subtle color variations
        ctx.globalAlpha = 0.05;
        for (let i = 0; i < 50; i++) {
            const x = Math.random() * 256;
            const y = Math.random() * 256;
            const size = Math.random() * 20 + 10;
            
            ctx.fillStyle = `hsl(${15 + Math.random() * 10}, 40%, ${70 + Math.random() * 10}%)`;
            ctx.beginPath();
            ctx.arc(x, y, size, 0, Math.PI * 2);
            ctx.fill();
        }
        
        const skinTexture = new THREE.CanvasTexture(canvas);
        material.map = skinTexture;
    }

    createUltraRealisticEyes() {
        // Create more anatomically correct eye sockets
        const socketGeometry = new THREE.SphereGeometry(0.078, 32, 32);
        const socketMaterial = new THREE.MeshLambertMaterial({ 
            color: 0xd8b896,
            transparent: true,
            opacity: 0.85
        });
        
        // Modify socket geometry for more realistic shape
        const socketVertices = socketGeometry.attributes.position.array;
        for (let i = 0; i < socketVertices.length; i += 3) {
            const y = socketVertices[i + 1];
            if (y > 0) {
                socketVertices[i + 1] *= 0.7; // Flatten top
            }
        }
        socketGeometry.attributes.position.needsUpdate = true;
        socketGeometry.computeVertexNormals();
        
        // Left eye socket
        const leftSocket = new THREE.Mesh(socketGeometry, socketMaterial);
        leftSocket.position.set(-0.08, 0.06, 0.20);
        leftSocket.scale.set(1.15, 0.65, 0.85);
        this.head.add(leftSocket);
        
        // Right eye socket
        const rightSocket = new THREE.Mesh(socketGeometry, socketMaterial);
        rightSocket.position.set(0.08, 0.06, 0.20);
        rightSocket.scale.set(1.15, 0.65, 0.85);
        this.head.add(rightSocket);
        
        // Ultra-realistic eyeballs with proper curvature
        const eyeballGeometry = new THREE.SphereGeometry(0.050, 32, 32);
        const eyeballMaterial = new THREE.MeshPhysicalMaterial({ 
            color: 0xffffff,
            roughness: 0.05,
            metalness: 0.0,
            transmission: 0.05,
            ior: 1.336,
            clearcoat: 0.9,
            clearcoatRoughness: 0.05
        });
        
        this.leftEyeball = new THREE.Mesh(eyeballGeometry, eyeballMaterial);
        this.leftEyeball.position.set(-0.08, 0.06, 0.22);
        this.leftEyeball.name = 'leftEyeball';
        this.head.add(this.leftEyeball);
        
        this.rightEyeball = new THREE.Mesh(eyeballGeometry, eyeballMaterial);
        this.rightEyeball.position.set(0.08, 0.06, 0.22);
        this.rightEyeball.name = 'rightEyeball';
        this.head.add(this.rightEyeball);
        
        // Enhanced iris with detailed texture and depth
        const irisGeometry = new THREE.CircleGeometry(0.024, 32);
        const irisMaterial = new THREE.MeshPhysicalMaterial({ 
            color: 0x2e5c8a,
            roughness: 0.7,
            metalness: 0.0,
            transparent: true,
            opacity: 0.9
        });
        
        // Create detailed iris pattern
        this.createDetailedIrisTexture(irisMaterial);
        
        const leftIris = new THREE.Mesh(irisGeometry, irisMaterial);
        leftIris.position.set(0, 0, 0.048);
        this.leftEyeball.add(leftIris);
        
        const rightIris = new THREE.Mesh(irisGeometry, irisMaterial);
        rightIris.position.set(0, 0, 0.048);
        this.rightEyeball.add(rightIris);
        
        // Pupils with proper depth and light reflection
        const pupilGeometry = new THREE.CircleGeometry(0.010, 16);
        const pupilMaterial = new THREE.MeshBasicMaterial({ color: 0x000000 });
        
        const leftPupil = new THREE.Mesh(pupilGeometry, pupilMaterial);
        leftPupil.position.set(0, 0, 0.002);
        leftIris.add(leftPupil);
        
        const rightPupil = new THREE.Mesh(pupilGeometry, pupilMaterial);
        rightPupil.position.set(0, 0, 0.002);
        rightIris.add(rightPupil);
        
        // Realistic eyelids with proper anatomy
        this.createAnatomicalEyelids();
        
        // Enhanced eye reflections
        this.createEnhancedEyeReflections();
    }

    createDetailedIrisTexture(material) {
        const canvas = document.createElement('canvas');
        canvas.width = 128;
        canvas.height = 128;
        const ctx = canvas.getContext('2d');
        
        const centerX = 64, centerY = 64;
        
        // Base iris color
        const gradient = ctx.createRadialGradient(centerX, centerY, 0, centerX, centerY, 60);
        gradient.addColorStop(0, '#4a7ca3');
        gradient.addColorStop(0.3, '#2e5c8a');
        gradient.addColorStop(0.7, '#1e3c5a');
        gradient.addColorStop(1, '#0f1e2f');
        
        ctx.fillStyle = gradient;
        ctx.beginPath();
        ctx.arc(centerX, centerY, 60, 0, Math.PI * 2);
        ctx.fill();
        
        // Iris radiating lines
        ctx.strokeStyle = 'rgba(100, 150, 200, 0.3)';
        ctx.lineWidth = 0.8;
        for (let i = 0; i < 30; i++) {
            const angle = (i / 30) * Math.PI * 2;
            const innerRadius = 15;
            const outerRadius = 55;
            
            ctx.beginPath();
            ctx.moveTo(
                centerX + Math.cos(angle) * innerRadius,
                centerY + Math.sin(angle) * innerRadius
            );
            ctx.lineTo(
                centerX + Math.cos(angle) * outerRadius,
                centerY + Math.sin(angle) * outerRadius
            );
            ctx.stroke();
        }
        
        // Add iris texture spots
        ctx.globalAlpha = 0.4;
        for (let i = 0; i < 50; i++) {
            const angle = Math.random() * Math.PI * 2;
            const radius = Math.random() * 35 + 10;
            const x = centerX + Math.cos(angle) * radius;
            const y = centerY + Math.sin(angle) * radius;
            
            ctx.fillStyle = `hsl(${210 + Math.random() * 20}, 60%, ${30 + Math.random() * 30}%)`;
            ctx.beginPath();
            ctx.arc(x, y, Math.random() * 2 + 0.5, 0, Math.PI * 2);
            ctx.fill();
        }
        
        const irisTexture = new THREE.CanvasTexture(canvas);
        material.map = irisTexture;
    }

    createAnatomicalEyelids() {
        // Upper eyelids with more realistic curvature
        const upperEyelidGeometry = new THREE.SphereGeometry(0.058, 24, 16, 0, Math.PI * 2, 0, Math.PI * 0.55);
        const eyelidMaterial = new THREE.MeshLambertMaterial({ 
            color: 0xf2c2a8,
            transparent: true,
            opacity: 0.92
        });
        
        // Modify eyelid geometry for better shape
        const eyelidVertices = upperEyelidGeometry.attributes.position.array;
        for (let i = 0; i < eyelidVertices.length; i += 3) {
            const x = eyelidVertices[i];
            const y = eyelidVertices[i + 1];
            
            // Create more natural eyelid curve
            eyelidVertices[i] *= (1 + Math.abs(x) * 0.1);
            eyelidVertices[i + 1] *= (1 - Math.abs(x) * 0.05);
        }
        upperEyelidGeometry.attributes.position.needsUpdate = true;
        upperEyelidGeometry.computeVertexNormals();
        
        this.leftUpperEyelid = new THREE.Mesh(upperEyelidGeometry, eyelidMaterial);
        this.leftUpperEyelid.position.set(-0.08, 0.088, 0.21);
        this.leftUpperEyelid.rotation.x = Math.PI;
        this.leftUpperEyelid.castShadow = true;
        this.leftUpperEyelid.name = 'leftUpperEyelid';
        this.head.add(this.leftUpperEyelid);
        
        this.rightUpperEyelid = new THREE.Mesh(upperEyelidGeometry, eyelidMaterial);
        this.rightUpperEyelid.position.set(0.08, 0.088, 0.21);
        this.rightUpperEyelid.rotation.x = Math.PI;
        this.rightUpperEyelid.castShadow = true;
        this.rightUpperEyelid.name = 'rightUpperEyelid';
        this.head.add(this.rightUpperEyelid);
        
        // Lower eyelids with proper anatomy
        const lowerEyelidGeometry = new THREE.SphereGeometry(0.054, 24, 16, 0, Math.PI * 2, 0, Math.PI * 0.35);
        
        this.leftLowerEyelid = new THREE.Mesh(lowerEyelidGeometry, eyelidMaterial);
        this.leftLowerEyelid.position.set(-0.08, 0.032, 0.21);
        this.leftLowerEyelid.name = 'leftLowerEyelid';
        this.head.add(this.leftLowerEyelid);
        
        this.rightLowerEyelid = new THREE.Mesh(lowerEyelidGeometry, eyelidMaterial);
        this.rightLowerEyelid.position.set(0.08, 0.032, 0.21);
        this.rightLowerEyelid.name = 'rightLowerEyelid';
        this.head.add(this.rightLowerEyelid);
        
        // Enhanced eyelashes
        this.createRealisticEyelashes();
    }

    createRealisticEyelashes() {
        const lashMaterial = new THREE.MeshLambertMaterial({ color: 0x2c1810 });
        
        // Upper eyelashes with more realistic distribution
        for (let eye = 0; eye < 2; eye++) {
            const eyelid = eye === 0 ? this.leftUpperEyelid : this.rightUpperEyelid;
            
            for (let i = 0; i < 15; i++) {
                const lashGeometry = new THREE.CylinderGeometry(0.0003, 0.0001, 0.018, 4);
                const lash = new THREE.Mesh(lashGeometry, lashMaterial);
                
                const angle = (i / 15) * Math.PI - Math.PI / 2;
                const radius = 0.048;
                const lengthVariation = 0.8 + Math.random() * 0.4;
                
                lash.position.set(
                    Math.cos(angle) * radius * 0.6,
                    0.025,
                    Math.sin(angle) * radius + 0.032
                );
                
                lash.rotation.z = angle + (Math.random() - 0.5) * 0.2;
                lash.rotation.x = Math.PI / 5 + Math.random() * 0.1;
                lash.scale.y = lengthVariation;
                
                eyelid.add(lash);
            }
            
            // Lower lashes (shorter and sparser)
            const lowerEyelid = eye === 0 ? this.leftLowerEyelid : this.rightLowerEyelid;
            
            for (let i = 0; i < 8; i++) {
                const lashGeometry = new THREE.CylinderGeometry(0.0002, 0.0001, 0.010, 4);
                const lash = new THREE.Mesh(lashGeometry, lashMaterial);
                
                const angle = (i / 8) * Math.PI - Math.PI / 2;
                const radius = 0.045;
                
                lash.position.set(
                    Math.cos(angle) * radius * 0.5,
                    -0.015,
                    Math.sin(angle) * radius + 0.025
                );
                
                lash.rotation.z = angle;
                lash.rotation.x = -Math.PI / 8;
                
                lowerEyelid.add(lash);
            }
        }
    }

    createEnhancedEyeReflections() {
        // Multiple reflection points for realism
        const reflectionMaterial = new THREE.MeshBasicMaterial({ 
            color: 0xffffff,
            transparent: true,
            opacity: 0.9
        });
        
        // Primary corneal reflection
        const primaryReflGeometry = new THREE.SphereGeometry(0.004, 8, 8);
        
        const leftPrimaryRefl = new THREE.Mesh(primaryReflGeometry, reflectionMaterial);
        leftPrimaryRefl.position.set(-0.018, 0.012, 0.049);
        this.leftEyeball.add(leftPrimaryRefl);
        
        const rightPrimaryRefl = new THREE.Mesh(primaryReflGeometry, reflectionMaterial);
        rightPrimaryRefl.position.set(-0.018, 0.012, 0.049);
        this.rightEyeball.add(rightPrimaryRefl);
        
        // Secondary smaller reflections
        const secondaryReflGeometry = new THREE.SphereGeometry(0.002, 6, 6);
        const secondaryReflMaterial = new THREE.MeshBasicMaterial({ 
            color: 0xffffff,
            transparent: true,
            opacity: 0.6
        });
        
        const leftSecondaryRefl = new THREE.Mesh(secondaryReflGeometry, secondaryReflMaterial);
        leftSecondaryRefl.position.set(0.015, -0.008, 0.048);
        this.leftEyeball.add(leftSecondaryRefl);
        
        const rightSecondaryRefl = new THREE.Mesh(secondaryReflGeometry, secondaryReflMaterial);
        rightSecondaryRefl.position.set(0.015, -0.008, 0.048);
        this.rightEyeball.add(rightSecondaryRefl);
    }

    createExpressiveEyebrows() {
        // Create more realistic eyebrow hair with better distribution
        const browMaterial = new THREE.MeshLambertMaterial({ color: 0x5d4037 });
        
        // Left eyebrow with natural hair growth pattern
        this.leftEyebrow = new THREE.Group();
        this.leftEyebrow.position.set(-0.08, 0.135, 0.22);
        this.leftEyebrow.name = 'leftEyebrow';
        
        for (let i = 0; i < 35; i++) {
            const hairGeometry = new THREE.CylinderGeometry(0.0006, 0.0003, 0.025, 4);
            const hair = new THREE.Mesh(hairGeometry, browMaterial);
            
            const x = (i - 17) * 0.006;
            const y = Math.sin(i * 0.2) * 0.004 + Math.random() * 0.002;
            const z = Math.cos(i * 0.15) * 0.008 + Math.random() * 0.003;
            
            hair.position.set(x, y, z);
            hair.rotation.z = (i - 17) * 0.04 + (Math.random() - 0.5) * 0.3;
            hair.rotation.x = Math.PI / 5 + Math.random() * 0.2;
            hair.rotation.y = (Math.random() - 0.5) * 0.1;
            
            // Hair length variation
            hair.scale.y = 0.7 + Math.random() * 0.6;
            
            this.leftEyebrow.add(hair);
        }
        this.head.add(this.leftEyebrow);
        
        // Right eyebrow (mirrored with slight asymmetry)
        this.rightEyebrow = this.leftEyebrow.clone();
        this.rightEyebrow.position.set(0.08, 0.135, 0.22);
        this.rightEyebrow.scale.x = -1;
        this.rightEyebrow.rotation.y = Math.PI;
        this.rightEyebrow.name = 'rightEyebrow';
        this.head.add(this.rightEyebrow);
    }

    createRealisticNose() {
        // More anatomically accurate nose structure
        const noseGroup = new THREE.Group();
        
        // Nose bridge with proper curvature
        const bridgeGeometry = new THREE.CylinderGeometry(0.008, 0.012, 0.09, 8);
        const noseMaterial = new THREE.MeshLambertMaterial({ color: 0xfdbcb4 });
        
        // Modify bridge for more realistic shape
        const bridgeVertices = bridgeGeometry.attributes.position.array;
        for (let i = 0; i < bridgeVertices.length; i += 3) {
            const y = bridgeVertices[i + 1];
            bridgeVertices[i + 2] += Math.abs(y) * 0.1; // Add forward curve
        }
        bridgeGeometry.attributes.position.needsUpdate = true;
        bridgeGeometry.computeVertexNormals();
        
        const bridge = new THREE.Mesh(bridgeGeometry, noseMaterial);
        bridge.position.set(0, 0.025, 0.015);
        bridge.rotation.x = -Math.PI / 12;
        noseGroup.add(bridge);
        
        // Nose tip with better definition
        const tipGeometry = new THREE.SphereGeometry(0.020, 16, 16);
        
        // Modify tip geometry for more realistic nose tip
        const tipVertices = tipGeometry.attributes.position.array;
        for (let i = 0; i < tipVertices.length; i += 3) {
            const x = tipVertices[i];
            const y = tipVertices[i + 1];
            const z = tipVertices[i + 2];
            
            // Create more pointed and defined tip
            tipVertices[i] *= (1 + Math.abs(y) * 0.1);
            tipVertices[i + 2] *= (1.2 - Math.abs(y) * 0.1);
        }
        tipGeometry.attributes.position.needsUpdate = true;
        tipGeometry.computeVertexNormals();
        
        const tip = new THREE.Mesh(tipGeometry, noseMaterial);
        tip.position.set(0, -0.025, 0.025);
        tip.scale.set(1, 0.8, 1.3);
        noseGroup.add(tip);
        
        // Enhanced nostrils with proper depth
        const nostrilGeometry = new THREE.SphereGeometry(0.007, 12, 12);
        const nostrilMaterial = new THREE.MeshLambertMaterial({ color: 0xc49574 });
        
        const leftNostril = new THREE.Mesh(nostrilGeometry, nostrilMaterial);
        leftNostril.position.set(-0.013, -0.030, 0.018);
        leftNostril.scale.set(0.7, 0.5, 1.4);
        leftNostril.rotation.z = -Math.PI / 8;
        noseGroup.add(leftNostril);
        
        const rightNostril = new THREE.Mesh(nostrilGeometry, nostrilMaterial);
        rightNostril.position.set(0.013, -0.030, 0.018);
        rightNostril.scale.set(0.7, 0.5, 1.4);
        rightNostril.rotation.z = Math.PI / 8;
        noseGroup.add(rightNostril);
        
        // Nose wings with better definition
        const wingGeometry = new THREE.SphereGeometry(0.018, 12, 12);
        
        const leftWing = new THREE.Mesh(wingGeometry, noseMaterial);
        leftWing.position.set(-0.020, -0.018, 0.012);
        leftWing.scale.set(0.6, 0.7, 0.8);
        noseGroup.add(leftWing);
        
        const rightWing = new THREE.Mesh(wingGeometry, noseMaterial);
        rightWing.position.set(0.020, -0.018, 0.012);
        rightWing.scale.set(0.6, 0.7, 0.8);
        noseGroup.add(rightWing);
        
        // Subtle nose highlight
        const highlightGeometry = new THREE.SphereGeometry(0.003, 8, 8);
        const highlightMaterial = new THREE.MeshBasicMaterial({ 
            color: 0xffffff,
            transparent: true,
            opacity: 0.3
        });
        
        const highlight = new THREE.Mesh(highlightGeometry, highlightMaterial);
        highlight.position.set(0, -0.015, 0.030);
        noseGroup.add(highlight);
        
        noseGroup.position.set(0, 0.02, 0.24);
        noseGroup.name = 'nose';
        this.head.add(noseGroup);
        this.nose = noseGroup;
    }

    createDetailedMouth() {
        const mouthGroup = new THREE.Group();
        
        // Mouth cavity with proper depth and shading
        const cavityGeometry = new THREE.CylinderGeometry(0.038, 0.032, 0.018, 16);
        const cavityMaterial = new THREE.MeshLambertMaterial({ 
            color: 0x2a0a0a,
            transparent: true,
            opacity: 0.9
        });
        
        this.mouthCavity = new THREE.Mesh(cavityGeometry, cavityMaterial);
        this.mouthCavity.rotation.x = Math.PI / 2;
        this.mouthCavity.position.set(0, 0, 0.012);
        mouthGroup.add(this.mouthCavity);
        
        // Enhanced lips with better anatomy
        const lipGeometry = new THREE.TorusGeometry(0.038, 0.009, 8, 24);
        const lipMaterial = new THREE.MeshPhysicalMaterial({ 
            color: 0xd65d5d,
            roughness: 0.3,
            metalness: 0.0,
            clearcoat: 0.4,
            clearcoatRoughness: 0.1,
            transparent: true,
            opacity: 0.95
        });
        
        this.lips = new THREE.Mesh(lipGeometry, lipMaterial);
        this.lips.rotation.x = Math.PI / 2;
        this.lips.castShadow = true;
        this.lips.name = 'lips';
        mouthGroup.add(this.lips);
        
        // Separate upper and lower lips for better expression control
        const upperLipGeometry = new THREE.TorusGeometry(0.038, 0.007, 8, 24, Math.PI);
        this.upperLip = new THREE.Mesh(upperLipGeometry, lipMaterial);
        this.upperLip.rotation.x = Math.PI / 2;
        this.upperLip.position.set(0, 0, 0.003);
        mouthGroup.add(this.upperLip);
        
        const lowerLipGeometry = new THREE.TorusGeometry(0.038, 0.010, 8, 24, Math.PI);
        this.lowerLip = new THREE.Mesh(lowerLipGeometry, lipMaterial);
        this.lowerLip.rotation.x = -Math.PI / 2;
        this.lowerLip.position.set(0, 0, 0.003);
        mouthGroup.add(this.lowerLip);
        
        // Lip highlight for realism
        const lipHighlightGeometry = new THREE.TorusGeometry(0.038, 0.002, 4, 16, Math.PI);
        const lipHighlightMaterial = new THREE.MeshBasicMaterial({ 
            color: 0xffffff,
            transparent: true,
            opacity: 0.4
        });
        
        const lipHighlight = new THREE.Mesh(lipHighlightGeometry, lipHighlightMaterial);
        lipHighlight.rotation.x = Math.PI / 2;
        lipHighlight.position.set(0, 0, 0.008);
        mouthGroup.add(lipHighlight);
        
        // Enhanced teeth with better realism
        this.createRealisticTeeth(mouthGroup);
        
        // Tongue with better anatomy
        this.createTongue(mouthGroup);
        
        mouthGroup.position.set(0, -0.08, 0.23);
        mouthGroup.name = 'mouth';
        this.head.add(mouthGroup);
        this.mouth = mouthGroup;
    }

    createRealisticTeeth(mouthGroup) {
        const teethGroup = new THREE.Group();
        const toothMaterial = new THREE.MeshPhysicalMaterial({ 
            color: 0xfbfbfb,
            roughness: 0.05,
            metalness: 0.0,
            clearcoat: 0.9,
            clearcoatRoughness: 0.05,
            transparent: true,
            opacity: 0.98
        });
        
        // Upper teeth with individual variations
        const upperTeethData = [
            { width: 0.005, height: 0.009, depth: 0.007 }, // Incisors
            { width: 0.005, height: 0.009, depth: 0.007 },
            { width: 0.004, height: 0.008, depth: 0.006 }, // Lateral incisors
            { width: 0.004, height: 0.008, depth: 0.006 },
            { width: 0.005, height: 0.010, depth: 0.008 }, // Canines
            { width: 0.005, height: 0.010, depth: 0.008 },
            { width: 0.004, height: 0.007, depth: 0.006 }, // Premolars
            { width: 0.004, height: 0.007, depth: 0.006 }
        ];
        
        upperTeethData.forEach((toothData, i) => {
            const toothGeometry = new THREE.BoxGeometry(toothData.width, toothData.height, toothData.depth);
            
            // Round the tooth for more realism
            const toothVertices = toothGeometry.attributes.position.array;
            for (let j = 0; j < toothVertices.length; j += 3) {
                const y = toothVertices[j + 1];
                if (y > 0) {
                    const factor = y / (toothData.height / 2);
                    toothVertices[j] *= (1 - factor * 0.2);
                    toothVertices[j + 2] *= (1 - factor * 0.2);
                }
            }
            toothGeometry.attributes.position.needsUpdate = true;
            toothGeometry.computeVertexNormals();
            
            const tooth = new THREE.Mesh(toothGeometry, toothMaterial);
            
            const angle = (i - 3.5) * 0.16;
            const radius = 0.030;
            
            tooth.position.set(
                Math.sin(angle) * radius,
                0.004,
                Math.cos(angle) * radius
            );
            tooth.rotation.y = angle;
            
            teethGroup.add(tooth);
        });
        
        // Lower teeth (slightly smaller)
        const lowerTeethData = upperTeethData.map(data => ({
            width: data.width * 0.9,
            height: data.height * 0.85,
            depth: data.depth * 0.9
        }));
        
        lowerTeethData.forEach((toothData, i) => {
            const toothGeometry = new THREE.BoxGeometry(toothData.width, toothData.height, toothData.depth);
            const tooth = new THREE.Mesh(toothGeometry, toothMaterial);
            
            const angle = (i - 3.5) * 0.16;
            const radius = 0.028;
            
            tooth.position.set(
                Math.sin(angle) * radius,
                -0.004,
                Math.cos(angle) * radius
            );
            tooth.rotation.y = angle;
            
            teethGroup.add(tooth);
        });
        
        teethGroup.visible = false;
        teethGroup.name = 'teeth';
        mouthGroup.add(teethGroup);
        this.teeth = teethGroup;
    }

    createTongue(mouthGroup) {
        const tongueGeometry = new THREE.SphereGeometry(0.028, 16, 16);
        
        // Modify tongue geometry for better shape
        const tongueVertices = tongueGeometry.attributes.position.array;
        for (let i = 0; i < tongueVertices.length; i += 3) {
            const x = tongueVertices[i];
            const y = tongueVertices[i + 1];
            const z = tongueVertices[i + 2];
            
            // Flatten and elongate tongue
            tongueVertices[i + 1] *= 0.3; // Flatten
            tongueVertices[i + 2] *= 1.3; // Elongate
            
            // Add groove down the middle
            if (Math.abs(x) < 0.005) {
                tongueVertices[i + 1] *= 0.8;
            }
        }
        tongueGeometry.attributes.position.needsUpdate = true;
        tongueGeometry.computeVertexNormals();
        
        const tongueMaterial = new THREE.MeshLambertMaterial({ 
            color: 0xef7b9d,
            transparent: true,
            opacity: 0.85
        });
        
        this.tongue = new THREE.Mesh(tongueGeometry, tongueMaterial);
        this.tongue.position.set(0, -0.008, 0.008);
        this.tongue.visible = false;
        this.tongue.name = 'tongue';
        
        mouthGroup.add(this.tongue);
    }

    createProfessionalHairstyle() {
        const hairGroup = new THREE.Group();
        const hairMaterial = new THREE.MeshLambertMaterial({ 
            color: 0x4a4a4a,
            transparent: true,
            opacity: 0.92
        });
        
        // Main hair volume with better shape
        const hairGeometry = new THREE.SphereGeometry(0.29, 32, 32, 0, Math.PI * 2, 0, Math.PI * 0.68);
        
        // Modify hair geometry for more realistic shape
        const hairVertices = hairGeometry.attributes.position.array;
        for (let i = 0; i < hairVertices.length; i += 3) {
            const x = hairVertices[i];
            const y = hairVertices[i + 1];
            const z = hairVertices[i + 2];
            
            // Create more natural hair volume
            const radius = Math.sqrt(x * x + z * z);
            const factor = 1 + Math.sin(radius * 10) * 0.02;
            
            hairVertices[i] *= factor;
            hairVertices[i + 2] *= factor;
            
            // Add slight backward sweep
            if (y > 0.1) {
                hairVertices[i + 2] -= y * 0.05;
            }
        }
        hairGeometry.attributes.position.needsUpdate = true;
        hairGeometry.computeVertexNormals();
        
        const mainHair = new THREE.Mesh(hairGeometry, hairMaterial);
        mainHair.position.set(0, 0.08, -0.025);
        mainHair.castShadow = true;
        hairGroup.add(mainHair);
        
        // Individual hair strands for added realism
        for (let i = 0; i < 80; i++) {
            const strandGeometry = new THREE.CylinderGeometry(0.0008, 0.0004, 0.045, 4);
            const strand = new THREE.Mesh(strandGeometry, hairMaterial);
            
            const angle = (i / 80) * Math.PI * 2;
            const radius = 0.24 + Math.random() * 0.10;
            const height = 0.12 + Math.random() * 0.18;
            
            strand.position.set(
                Math.cos(angle) * radius,
                height,
                Math.sin(angle) * radius * 0.6 - 0.02
            );
            
            strand.rotation.z = Math.random() * 0.6 - 0.3;
            strand.rotation.x = Math.random() * 0.4;
            strand.rotation.y = Math.random() * 0.2;
            
            // Vary strand length
            strand.scale.y = 0.7 + Math.random() * 0.6;
            
            hairGroup.add(strand);
        }
        
        // Professional side part with better definition
        const partGeometry = new THREE.BoxGeometry(0.45, 0.003, 0.35);
        const partMaterial = new THREE.MeshLambertMaterial({ 
            color: 0xfdbcb4,
            transparent: true,
            opacity: 0.25
        });
        
        const part = new THREE.Mesh(partGeometry, partMaterial);
        part.position.set(0.06, 0.27, -0.02);
        part.rotation.z = Math.PI / 24;
        hairGroup.add(part);
        
        // Hair texture and highlights
        this.addHairHighlights(hairGroup);
        
        hairGroup.name = 'hair';
        this.head.add(hairGroup);
        this.hair = hairGroup;
    }

    addHairHighlights(hairGroup) {
        const highlightMaterial = new THREE.MeshBasicMaterial({ 
            color: 0x6a6a6a,
            transparent: true,
            opacity: 0.3
        });
        
        // Add subtle highlights
        for (let i = 0; i < 15; i++) {
            const highlightGeometry = new THREE.CylinderGeometry(0.001, 0.0005, 0.03, 4);
            const highlight = new THREE.Mesh(highlightGeometry, highlightMaterial);
            
            const angle = Math.random() * Math.PI * 2;
            const radius = 0.22 + Math.random() * 0.06;
            const height = 0.15 + Math.random() * 0.12;
            
            highlight.position.set(
                Math.cos(angle) * radius,
                height,
                Math.sin(angle) * radius * 0.6
            );
            
            highlight.rotation.z = Math.random() * 0.4 - 0.2;
            highlight.rotation.x = Math.random() * 0.3;
            
            hairGroup.add(highlight);
        }
    }

    createDetailedEars() {
        const earMaterial = new THREE.MeshLambertMaterial({ color: 0xfdbcb4 });
        
        // Left ear with enhanced anatomy
        const leftEarGroup = new THREE.Group();
        
        // Outer helix
        const helixGeometry = new THREE.TorusGeometry(0.038, 0.009, 8, 16, Math.PI * 1.4);
        const helix = new THREE.Mesh(helixGeometry, earMaterial);
        helix.rotation.y = Math.PI / 2;
        helix.rotation.z = Math.PI / 6;
        helix.castShadow = true;
        leftEarGroup.add(helix);
        
        // Antihelix
        const antihelixGeometry = new THREE.TorusGeometry(0.025, 0.005, 6, 12, Math.PI);
        const antihelix = new THREE.Mesh(antihelixGeometry, earMaterial);
        antihelix.position.set(0, 0.005, 0.01);
        antihelix.rotation.y = Math.PI / 2;
        antihelix.rotation.z = Math.PI / 8;
        leftEarGroup.add(antihelix);
        
        // Ear lobe with better shape
        const lobeGeometry = new THREE.SphereGeometry(0.016, 12, 12);
        const lobe = new THREE.Mesh(lobeGeometry, earMaterial);
        lobe.position.set(0, -0.032, 0.012);
        lobe.scale.set(0.8, 1.3, 0.6);
        lobe.castShadow = true;
        leftEarGroup.add(lobe);
        
        // Ear canal
        const canalGeometry = new THREE.CylinderGeometry(0.008, 0.005, 0.015, 8);
        const canalMaterial = new THREE.MeshLambertMaterial({ color: 0xd4a574 });
        const canal = new THREE.Mesh(canalGeometry, canalMaterial);
        canal.position.set(0, 0, 0.018);
        canal.rotation.y = Math.PI / 2;
        leftEarGroup.add(canal);
        
        // Tragus
        const tragusGeometry = new THREE.SphereGeometry(0.008, 8, 8);
        const tragus = new THREE.Mesh(tragusGeometry, earMaterial);
        tragus.position.set(0, -0.008, 0.022);
        tragus.scale.set(0.6, 1.2, 0.8);
        leftEarGroup.add(tragus);
        
        leftEarGroup.position.set(-0.26, 0.05, -0.02);
        leftEarGroup.rotation.y = -Math.PI / 5;
        leftEarGroup.rotation.z = Math.PI / 12;
        leftEarGroup.castShadow = true;
        this.head.add(leftEarGroup);
        
        // Right ear (mirrored)
        const rightEarGroup = leftEarGroup.clone();
        rightEarGroup.position.set(0.26, 0.05, -0.02);
        rightEarGroup.rotation.y = Math.PI / 5;
        rightEarGroup.rotation.z = -Math.PI / 12;
        rightEarGroup.scale.x = -1;
        this.head.add(rightEarGroup);
    }

    createFacialDetails() {
        // Enhanced cheekbones
        const cheekMaterial = new THREE.MeshLambertMaterial({ 
            color: 0xf0bb9a,
            transparent: true,
            opacity: 0.6
        });
        
        const cheekGeometry = new THREE.SphereGeometry(0.022, 16, 16);
        
        const leftCheek = new THREE.Mesh(cheekGeometry, cheekMaterial);
        leftCheek.position.set(-0.125, 0.02, 0.185);
        leftCheek.scale.set(1.3, 0.5, 0.7);
        this.head.add(leftCheek);
        
        const rightCheek = new THREE.Mesh(cheekGeometry, cheekMaterial);
        rightCheek.position.set(0.125, 0.02, 0.185);
        rightCheek.scale.set(1.3, 0.5, 0.7);
        this.head.add(rightCheek);
        
        // Facial structure enhancements
        this.createFacialLines();
        this.createJawlineDefinition();
        this.addSkinBlemishes();
    }

    createFacialLines() {
        const lineMaterial = new THREE.MeshBasicMaterial({ 
            color: 0xd8a882,
            transparent: true,
            opacity: 0.25
        });
        
        // Nasolabial folds (smile lines) - more pronounced
        const foldGeometry = new THREE.CylinderGeometry(0.0008, 0.0008, 0.065, 4);
        
        const leftFold = new THREE.Mesh(foldGeometry, lineMaterial);
        leftFold.position.set(-0.065, -0.025, 0.22);
        leftFold.rotation.z = Math.PI / 5;
        this.head.add(leftFold);
        
        const rightFold = new THREE.Mesh(foldGeometry, lineMaterial);
        rightFold.position.set(0.065, -0.025, 0.22);
        rightFold.rotation.z = -Math.PI / 5;
        this.head.add(rightFold);
        
        // Crow's feet (very subtle)
        const crowsFeetsGeometry = new THREE.CylinderGeometry(0.0003, 0.0003, 0.015, 3);
        
        for (let i = 0; i < 3; i++) {
            const leftCrowsFeet = new THREE.Mesh(crowsFeetsGeometry, lineMaterial);
            leftCrowsFeet.position.set(-0.12, 0.08 - i * 0.01, 0.18);
            leftCrowsFeet.rotation.z = Math.PI / 4 + i * 0.1;
            this.head.add(leftCrowsFeet);
            
            const rightCrowsFeet = new THREE.Mesh(crowsFeetsGeometry, lineMaterial);
            rightCrowsFeet.position.set(0.12, 0.08 - i * 0.01, 0.18);
            rightCrowsFeet.rotation.z = -Math.PI / 4 - i * 0.1;
            this.head.add(rightCrowsFeet);
        }
    }

    createJawlineDefinition() {
        const jawMaterial = new THREE.MeshLambertMaterial({ 
            color: 0xf2c2a8,
            transparent: true,
            opacity: 0.4
        });
        
        // Jawline enhancement
        const jawGeometry = new THREE.CylinderGeometry(0.015, 0.018, 0.3, 8);
        const jaw = new THREE.Mesh(jawGeometry, jawMaterial);
        jaw.position.set(0, -0.15, 0.12);
        jaw.rotation.z = Math.PI / 2;
        jaw.scale.y = 1.2;
        this.head.add(jaw);
        
        // Chin definition
        const chinGeometry = new THREE.SphereGeometry(0.025, 12, 12);
        const chin = new THREE.Mesh(chinGeometry, jawMaterial);
        chin.position.set(0, -0.22, 0.15);
        chin.scale.set(0.8, 0.6, 1.2);
        this.head.add(chin);
    }

    addSkinBlemishes() {
        // Very subtle skin imperfections for realism
        const blemishMaterial = new THREE.MeshBasicMaterial({ 
            color: 0xe8a690,
            transparent: true,
            opacity: 0.15
        });
        
        // Add a few very small skin marks
        for (let i = 0; i < 5; i++) {
            const blemishGeometry = new THREE.SphereGeometry(0.002, 6, 6);
            const blemish = new THREE.Mesh(blemishGeometry, blemishMaterial);
            
            // Random positions on face
            const angle = Math.random() * Math.PI * 2;
            const radius = 0.15 + Math.random() * 0.08;
            const height = -0.05 + Math.random() * 0.15;
            
            blemish.position.set(
                Math.cos(angle) * radius,
                height,
                0.18 + Math.sin(angle) * radius * 0.3
            );
            
            this.head.add(blemish);
        }
    }

    createProfessionalBody() {
        // Professional medical attire with high detail
        const torsoGeometry = new THREE.CylinderGeometry(0.32, 0.38, 0.85, 32);
        const labCoatMaterial = new THREE.MeshPhysicalMaterial({ 
            color: 0xffffff,
            roughness: 0.85,
            metalness: 0.0,
            clearcoat: 0.08,
            clearcoatRoughness: 0.95
        });
        
        this.torso = new THREE.Mesh(torsoGeometry, labCoatMaterial);
        this.torso.position.set(0, 0.8, 0);
        this.torso.castShadow = true;
        this.torso.receiveShadow = true;
        this.torso.name = 'torso';
        
        // Enhanced lab coat details
        this.addProfessionalLabCoatDetails();
        
        // Professional shirt underneath
        const shirtGeometry = new THREE.CylinderGeometry(0.29, 0.35, 0.45, 32);
        const shirtMaterial = new THREE.MeshLambertMaterial({ color: 0x4a90e2 });
        
        const shirt = new THREE.Mesh(shirtGeometry, shirtMaterial);
        shirt.position.set(0, 0.2, 0);
        this.torso.add(shirt);
        
        // Professional tie with pattern
        this.createProfessionalTie();
        
        this.doctorModel.add(this.torso);
    }

    addProfessionalLabCoatDetails() {
        // High-quality buttons
        for (let i = 0; i < 6; i++) {
            const buttonGeometry = new THREE.CylinderGeometry(0.012, 0.012, 0.003, 16);
            const buttonMaterial = new THREE.MeshPhysicalMaterial({ 
                color: 0x2c3e50,
                roughness: 0.2,
                metalness: 0.1,
                clearcoat: 0.8,
                clearcoatRoughness: 0.2
            });
            
            const button = new THREE.Mesh(buttonGeometry, buttonMaterial);
            button.position.set(0, 0.35 - (i * 0.11), 0.33);
            button.rotation.x = Math.PI / 2;
            button.castShadow = true;
            this.torso.add(button);
            
            // Button holes
            const holeGeometry = new THREE.CylinderGeometry(0.003, 0.003, 0.001, 8);
            const holeMaterial = new THREE.MeshBasicMaterial({ color: 0x000000 });
            
            for (let j = 0; j < 4; j++) {
                const hole = new THREE.Mesh(holeGeometry, holeMaterial);
                const angle = (j / 4) * Math.PI * 2;
                hole.position.set(
                    Math.cos(angle) * 0.005,
                    0.001,
                    Math.sin(angle) * 0.005
                );
                button.add(hole);
            }
        }
        
        // Professional collar with proper structure
        const collarGeometry = new THREE.BoxGeometry(0.35, 0.02, 0.12);
        const collarMaterial = new THREE.MeshLambertMaterial({ color: 0xffffff });
        
        const collar = new THREE.Mesh(collarGeometry, collarMaterial);
        collar.position.set(0, 0.4, 0.25);
        collar.rotation.x = -Math.PI / 4;
        collar.castShadow = true;
        this.torso.add(collar);
        
        // Detailed pockets with stitching
        this.createDetailedPockets();
        
        // Lab coat lapels
        this.createLabCoatLapels();
    }

    createDetailedPockets() {
        const pocketMaterial = new THREE.MeshLambertMaterial({ color: 0xf8f8f8 });
        
        // Left pocket
        const leftPocketGeometry = new THREE.BoxGeometry(0.14, 0.1, 0.008);
        const leftPocket = new THREE.Mesh(leftPocketGeometry, pocketMaterial);
        leftPocket.position.set(-0.16, 0.05, 0.32);
        leftPocket.castShadow = true;
        this.torso.add(leftPocket);
        
        // Right pocket
        const rightPocket = new THREE.Mesh(leftPocketGeometry, pocketMaterial);
        rightPocket.position.set(0.16, 0.05, 0.32);
        rightPocket.castShadow = true;
        this.torso.add(rightPocket);
        
        // Pocket stitching details
        const stitchMaterial = new THREE.MeshBasicMaterial({ color: 0xe0e0e0 });
        
        [leftPocket, rightPocket].forEach(pocket => {
            // Top stitch
            const topStitchGeometry = new THREE.BoxGeometry(0.14, 0.001, 0.001);
            const topStitch = new THREE.Mesh(topStitchGeometry, stitchMaterial);
            topStitch.position.set(0, 0.05, 0.001);
            pocket.add(topStitch);
            
            // Side stitches
            const sideStitchGeometry = new THREE.BoxGeometry(0.001, 0.1, 0.001);
            const leftStitch = new THREE.Mesh(sideStitchGeometry, stitchMaterial);
            leftStitch.position.set(-0.07, 0, 0.001);
            pocket.add(leftStitch);
            
            const rightStitch = new THREE.Mesh(sideStitchGeometry, stitchMaterial);
            rightStitch.position.set(0.07, 0, 0.001);
            pocket.add(rightStitch);
        });
    }

    createLabCoatLapels() {
        const lapelMaterial = new THREE.MeshLambertMaterial({ color: 0xffffff });
        
        // Left lapel
        const lapelGeometry = new THREE.BoxGeometry(0.08, 0.2, 0.01);
        const leftLapel = new THREE.Mesh(lapelGeometry, lapelMaterial);
        leftLapel.position.set(-0.12, 0.3, 0.31);
        leftLapel.rotation.z = Math.PI / 8;
        leftLapel.castShadow = true;
        this.torso.add(leftLapel);
        
        // Right lapel
        const rightLapel = new THREE.Mesh(lapelGeometry, lapelMaterial);
        rightLapel.position.set(0.12, 0.3, 0.31);
        rightLapel.rotation.z = -Math.PI / 8;
        rightLapel.castShadow = true;
        this.torso.add(rightLapel);
    }

    createProfessionalTie() {
        // Tie with professional pattern
        const tieGeometry = new THREE.BoxGeometry(0.08, 0.4, 0.008);
        const tieMaterial = new THREE.MeshPhysicalMaterial({ 
            color: 0x1a365d,
            roughness: 0.7,
            metalness: 0.0,
            clearcoat: 0.2
        });
        
        // Create tie pattern
        const tieCanvas = document.createElement('canvas');
        tieCanvas.width = 64;
        tieCanvas.height = 256;
        const tieCtx = tieCanvas.getContext('2d');
        
        // Base color
        tieCtx.fillStyle = '#1a365d';
        tieCtx.fillRect(0, 0, 64, 256);
        
        // Diagonal stripes pattern
        tieCtx.strokeStyle = '#2d5aa0';
        tieCtx.lineWidth = 2;
        for (let i = 0; i < 20; i++) {
            tieCtx.beginPath();
            tieCtx.moveTo(0, i * 20);
            tieCtx.lineTo(64, i * 20 + 32);
            tieCtx.stroke();
        }
        
        const tieTexture = new THREE.CanvasTexture(tieCanvas);
        tieMaterial.map = tieTexture;
        
        const tie = new THREE.Mesh(tieGeometry, tieMaterial);
        tie.position.set(0, 0.15, 0.325);
        tie.castShadow = true;
        this.torso.add(tie);
        
        // Tie knot
        const knotGeometry = new THREE.SphereGeometry(0.025, 16, 16);
        knotGeometry.scale(1, 0.6, 1.2);
        const knot = new THREE.Mesh(knotGeometry, tieMaterial);
        knot.position.set(0, 0.35, 0.32);
        knot.castShadow = true;
        this.torso.add(knot);
    }

    createAnatomicallyCorrectArms() {
        // Create arms with proper anatomical proportions and positioning
        this.createAnatomicalArm('left', -0.42);
        this.createAnatomicalArm('right', 0.42);
    }

    createAnatomicalArm(side, xOffset) {
        const armGroup = new THREE.Group();
        armGroup.name = `${side}ArmGroup`;
        
        // Shoulder connection
        const shoulderGeometry = new THREE.SphereGeometry(0.08, 16, 16);
        const shoulderMaterial = new THREE.MeshLambertMaterial({ color: 0xffffff });
        
        const shoulder = new THREE.Mesh(shoulderGeometry, shoulderMaterial);
        shoulder.position.set(0, 0, 0);
        shoulder.scale.set(1, 0.7, 0.8);
        shoulder.castShadow = true;
        armGroup.add(shoulder);
        
        // Upper arm with proper muscle definition
        const upperArmGeometry = new THREE.CylinderGeometry(0.055, 0.075, 0.45, 16);
        
        // Modify geometry for more realistic arm shape
        const upperArmVertices = upperArmGeometry.attributes.position.array;
        for (let i = 0; i < upperArmVertices.length; i += 3) {
            const y = upperArmVertices[i + 1];
            const radius = Math.sqrt(upperArmVertices[i] ** 2 + upperArmVertices[i + 2] ** 2);
            
            // Create muscle definition
            const muscleFactor = 1 + Math.sin(y * 4) * 0.05;
            upperArmVertices[i] *= muscleFactor;
            upperArmVertices[i + 2] *= muscleFactor;
        }
        upperArmGeometry.attributes.position.needsUpdate = true;
        upperArmGeometry.computeVertexNormals();
        
        const sleeveMaterial = new THREE.MeshLambertMaterial({ color: 0xffffff });
        const upperArm = new THREE.Mesh(upperArmGeometry, sleeveMaterial);
        upperArm.position.set(0, -0.22, 0);
        upperArm.castShadow = true;
        upperArm.receiveShadow = true;
        upperArm.name = `${side}UpperArm`;
        
        // Elbow joint
        const elbowGeometry = new THREE.SphereGeometry(0.055, 16, 16);
        const elbow = new THREE.Mesh(elbowGeometry, sleeveMaterial);
        elbow.position.set(0, -0.225, 0);
        elbow.scale.set(1.1, 0.8, 1.1);
        elbow.castShadow = true;
        upperArm.add(elbow);
        
        // Forearm with proper tapering
        const forearmGeometry = new THREE.CylinderGeometry(0.045, 0.055, 0.4, 16);
        const forearm = new THREE.Mesh(forearmGeometry, sleeveMaterial);
        forearm.position.set(0, -0.425, 0);
        
        // Better arm positioning - more natural angle and rotation
        if (side === 'left') {
            forearm.rotation.x = -Math.PI / 8; // Slight bend inward
            forearm.rotation.z = -Math.PI / 12; // Slight outward angle
        } else {
            forearm.rotation.x = Math.PI / 8; // Slight bend inward
            forearm.rotation.z = Math.PI / 12; // Slight outward angle
        }
        
        forearm.castShadow = true;
        forearm.receiveShadow = true;
        forearm.name = `${side}Forearm`;
        
        // Wrist
        const wristGeometry = new THREE.CylinderGeometry(0.04, 0.045, 0.06, 16);
        const wrist = new THREE.Mesh(wristGeometry, new THREE.MeshLambertMaterial({ color: 0xfdbcb4 }));
        wrist.position.set(0, -0.2, 0);
        wrist.castShadow = true;
        forearm.add(wrist);
        
        // Detailed hand
        const hand = this.createDetailedHand(side);
        hand.position.set(0, -0.23, 0);
        forearm.add(hand);
        
        // Sleeve details
        this.addSleeveDetails(upperArm, side);
        this.addSleeveDetails(forearm, side);
        
        upperArm.add(forearm);
        armGroup.add(upperArm);
        
        // Improved arm positioning - more natural shoulder placement
        armGroup.position.set(xOffset, 1.15, 0.05); // Slightly forward and higher
        
        // Better initial arm rotation - more natural resting position
        if (side === 'left') {
            armGroup.rotation.z = Math.PI / 12; // 15 degrees from vertical
            armGroup.rotation.x = Math.PI / 24; // Slight forward angle
        } else {
            armGroup.rotation.z = -Math.PI / 12; // 15 degrees from vertical
            armGroup.rotation.x = Math.PI / 24; // Slight forward angle
        }
        
        this.doctorModel.add(armGroup);
        
        // Store references
        this[`${side}ArmGroup`] = armGroup;
        this[`${side}UpperArm`] = upperArm;
        this[`${side}Forearm`] = forearm;
        this[`${side}Hand`] = hand;
    }

    createDetailedHand(side) {
        const handGroup = new THREE.Group();
        const handMaterial = new THREE.MeshLambertMaterial({ color: 0xfdbcb4 });
        
        // Palm with proper anatomy
        const palmGeometry = new THREE.BoxGeometry(0.08, 0.02, 0.12);
        
        // Modify palm for more realistic shape
        const palmVertices = palmGeometry.attributes.position.array;
        for (let i = 0; i < palmVertices.length; i += 3) {
            const x = palmVertices[i];
            const z = palmVertices[i + 2];
            
            // Create palm curvature
            palmVertices[i + 1] += Math.abs(x) * 0.1 + Math.abs(z) * 0.05;
        }
        palmGeometry.attributes.position.needsUpdate = true;
        palmGeometry.computeVertexNormals();
        
        const palm = new THREE.Mesh(palmGeometry, handMaterial);
        palm.castShadow = true;
        palm.receiveShadow = true;
        handGroup.add(palm);
        
        // Detailed fingers
        this.addDetailedFingers(handGroup, side, handMaterial);
        
        // Thumb with proper positioning
        this.addDetailedThumb(handGroup, side, handMaterial);
        
        // Hand details (knuckles, lines)
        this.addHandDetails(handGroup, handMaterial);
        
        handGroup.name = `${side}Hand`;
        return handGroup;
    }

    addDetailedFingers(handGroup, side, handMaterial) {
        const fingerPositions = [
            { x: -0.03, z: 0.06, name: 'index' },
            { x: -0.01, z: 0.065, name: 'middle' },
            { x: 0.01, z: 0.06, name: 'ring' },
            { x: 0.03, z: 0.055, name: 'pinky' }
        ];
        
        fingerPositions.forEach((pos, index) => {
            const fingerGroup = new THREE.Group();
            
            // Finger segments (3 per finger)
            let previousSegment = fingerGroup; // Start with fingerGroup
            for (let segment = 0; segment < 3; segment++) {
                const segmentLength = segment === 0 ? 0.025 : (segment === 1 ? 0.02 : 0.015);
                const segmentRadius = 0.006 - (segment * 0.001) - (index === 3 ? 0.002 : 0);
                
                const segmentGeometry = new THREE.CylinderGeometry(
                    segmentRadius * 0.8, 
                    segmentRadius, 
                    segmentLength, 
                    8
                );
                
                const fingerSegment = new THREE.Mesh(segmentGeometry, handMaterial);
                fingerSegment.castShadow = true;
                
                if (segment === 0) {
                    // First segment - add to fingerGroup
                    fingerSegment.position.set(0, segmentLength / 2, 0);
                    fingerGroup.add(fingerSegment);
                } else {
                    // Subsequent segments - add to previous segment
                    fingerSegment.position.set(0, segmentLength, 0);
                    previousSegment.add(fingerSegment);
                }
                
                previousSegment = fingerSegment; // Update reference for next iteration
                
                // Finger joints
                if (segment < 2) {
                    const jointGeometry = new THREE.SphereGeometry(segmentRadius * 1.1, 8, 8);
                    const joint = new THREE.Mesh(jointGeometry, handMaterial);
                    joint.position.set(0, segmentLength, 0);
                    joint.scale.set(1, 0.6, 1);
                    fingerSegment.add(joint);
                }
                
                // Fingernail on last segment
                if (segment === 2) {
                    const nailGeometry = new THREE.SphereGeometry(segmentRadius * 0.7, 8, 8);
                    const nailMaterial = new THREE.MeshPhysicalMaterial({ 
                        color: 0xffeee6,
                        roughness: 0.1,
                        metalness: 0.0,
                        clearcoat: 0.8
                    });
                    
                    const nail = new THREE.Mesh(nailGeometry, nailMaterial);
                    nail.position.set(0, segmentLength * 0.8, segmentRadius * 0.5);
                    nail.scale.set(0.8, 0.3, 0.6);
                    fingerSegment.add(nail);
                }
            }
            
            fingerGroup.position.set(pos.x, 0.01, pos.z);
            fingerGroup.rotation.x = -Math.PI / 12; // Slight natural curl
            fingerGroup.name = `${side}${pos.name}Finger`;
            
            handGroup.add(fingerGroup);
        });
    }

    addDetailedThumb(handGroup, side, handMaterial) {
        const thumbGroup = new THREE.Group();
        
        // Thumb has 2 main segments
        let previousSegment = thumbGroup;
        for (let segment = 0; segment < 2; segment++) {
            const segmentLength = segment === 0 ? 0.02 : 0.018;
            const segmentRadius = 0.008 - (segment * 0.001);
            
            const segmentGeometry = new THREE.CylinderGeometry(
                segmentRadius * 0.8, 
                segmentRadius, 
                segmentLength, 
                8
            );
            
            const thumbSegment = new THREE.Mesh(segmentGeometry, handMaterial);
            thumbSegment.castShadow = true;
            
            if (segment === 0) {
                thumbSegment.position.set(0, segmentLength / 2, 0);
                thumbGroup.add(thumbSegment);
            } else {
                thumbSegment.position.set(0, segmentLength, 0);
                previousSegment.add(thumbSegment);
                
                // Thumbnail
                const nailGeometry = new THREE.SphereGeometry(segmentRadius * 0.7, 8, 8);
                const nailMaterial = new THREE.MeshPhysicalMaterial({ 
                    color: 0xffeee6,
                    roughness: 0.1,
                    metalness: 0.0,
                    clearcoat: 0.8
                });
                
                const nail = new THREE.Mesh(nailGeometry, nailMaterial);
                nail.position.set(0, segmentLength * 0.8, segmentRadius * 0.5);
                nail.scale.set(0.8, 0.3, 0.6);
                thumbSegment.add(nail);
            }
            
            previousSegment = thumbSegment;
            
            // Thumb joint
            if (segment === 0) {
                const jointGeometry = new THREE.SphereGeometry(segmentRadius * 1.1, 8, 8);
                const joint = new THREE.Mesh(jointGeometry, handMaterial);
                joint.position.set(0, segmentLength, 0);
                joint.scale.set(1, 0.6, 1);
                thumbSegment.add(joint);
            }
        }
        
        // Position thumb properly
        const thumbXOffset = side === 'left' ? -0.04 : 0.04;
        thumbGroup.position.set(thumbXOffset, 0, 0.02);
        thumbGroup.rotation.z = side === 'left' ? Math.PI / 4 : -Math.PI / 4;
        thumbGroup.rotation.y = side === 'left' ? -Math.PI / 6 : Math.PI / 6;
        thumbGroup.name = `${side}Thumb`;
        
        handGroup.add(thumbGroup);
    }

    addHandDetails(handGroup, handMaterial) {
        // Knuckles
        const knucklePositions = [-0.03, -0.01, 0.01, 0.03];
        knucklePositions.forEach(x => {
            const knuckleGeometry = new THREE.SphereGeometry(0.004, 8, 8);
            const knuckle = new THREE.Mesh(knuckleGeometry, handMaterial);
            knuckle.position.set(x, 0.015, 0.04);
            knuckle.scale.set(1, 0.5, 1);
            handGroup.add(knuckle);
        });
        
        // Palm lines (very subtle)
        const lineMaterial = new THREE.MeshBasicMaterial({ 
            color: 0xe0b48a,
            transparent: true,
            opacity: 0.2
        });
        
        const lineGeometry = new THREE.CylinderGeometry(0.0005, 0.0005, 0.06, 4);
        
        // Life line
        const lifeLine = new THREE.Mesh(lineGeometry, lineMaterial);
        lifeLine.position.set(-0.02, 0.01, 0);
        lifeLine.rotation.z = Math.PI / 4;
        handGroup.add(lifeLine);
        
        // Heart line
        const heartLine = new THREE.Mesh(lineGeometry, lineMaterial);
        heartLine.position.set(0, 0.01, 0.02);
        heartLine.rotation.z = Math.PI / 6;
        handGroup.add(heartLine);
    }

    addSleeveDetails(armPart, side) {
        // Sleeve cuffs
        const cuffGeometry = new THREE.TorusGeometry(0.06, 0.008, 8, 24);
        const cuffMaterial = new THREE.MeshLambertMaterial({ color: 0xf8f8f8 });
        
        const cuff = new THREE.Mesh(cuffGeometry, cuffMaterial);
        cuff.position.set(0, -0.2, 0);
        cuff.castShadow = true;
        armPart.add(cuff);
        
        // Sleeve buttons on cuffs
        if (armPart.name.includes('Forearm')) {
            for (let i = 0; i < 2; i++) {
                const buttonGeometry = new THREE.CylinderGeometry(0.004, 0.004, 0.002, 8);
                const buttonMaterial = new THREE.MeshPhongMaterial({ 
                    color: 0x2c3e50,
                    shininess: 100
                });
                
                const button = new THREE.Mesh(buttonGeometry, buttonMaterial);
                button.position.set(0.02 - i * 0.04, 0, 0.05);
                button.rotation.x = Math.PI / 2;
                cuff.add(button);
            }
        }
    }

    createDetailedAccessories() {
        // Stethoscope
        const stethGroup = new THREE.Group();
        
        // Stethoscope tubing
        const tubingMaterial = new THREE.MeshLambertMaterial({ color: 0x2c3e50 });
        const tubingGeometry = new THREE.TorusGeometry(0.15, 0.008, 8, 32, Math.PI);
        
        const tubing = new THREE.Mesh(tubingGeometry, tubingMaterial);
        tubing.position.set(0, 0.3, 0.1);
        tubing.rotation.x = Math.PI / 6;
        stethGroup.add(tubing);
        
        // Chest piece
        const chestGeometry = new THREE.CylinderGeometry(0.02, 0.02, 0.01, 16);
        const chestMaterial = new THREE.MeshPhysicalMaterial({ color: 0x2c3e50, metalness: 0.5 });
        
        const chestPiece = new THREE.Mesh(chestGeometry, chestMaterial);
        chestPiece.position.set(0.15, 0.15, 0.2);
        stethGroup.add(chestPiece);
        
        this.doctorModel.add(stethGroup);
    }

    createMedicalEnvironment() {
        // Medical equipment in background (simplified)
        const equipmentGroup = new THREE.Group();
        
        // Medical chart
        const chartGeometry = new THREE.PlaneGeometry(0.3, 0.4);
        const chartMaterial = new THREE.MeshLambertMaterial({ color: 0xffffff });
        
        const chart = new THREE.Mesh(chartGeometry, chartMaterial);
        chart.position.set(-1.5, 1.5, -1);
        chart.rotation.y = Math.PI / 6;
        equipmentGroup.add(chart);
        
        this.scene.add(equipmentGroup);
    }

    setupAdvancedAnimations() {
        this.mixer = new THREE.AnimationMixer(this.doctorModel);
        
        this.createUltraRealisticTalkingAnimation();
        this.createProfessionalNoteWritingAnimation();
        this.createNaturalIdleAnimation();
        this.createAdvancedThinkingAnimation();
        this.createProfessionalGestureAnimations();
    }

    createUltraRealisticTalkingAnimation() {
        const tracks = [];
        const duration = 4.0;
        
        // Natural head movement with micro-expressions
        const headRotationTimes = [];
        const headRotationValues = [];
        const numFrames = 60;
        
        for (let i = 0; i <= numFrames; i++) {
            const t = (i / numFrames) * duration;
            headRotationTimes.push(t);
            
            // Create natural head movement pattern
            const baseRotY = Math.sin(t * 1.2) * 0.05;
            const baseRotX = Math.sin(t * 0.8 + Math.PI / 3) * 0.03;
            const baseRotZ = Math.sin(t * 0.6 + Math.PI / 6) * 0.02;
            
            // Add micro-movements
            const microRotY = Math.sin(t * 8) * 0.01;
            const microRotX = Math.sin(t * 6.5) * 0.008;
            
            const quaternion = new THREE.Quaternion();
            quaternion.setFromEuler(new THREE.Euler(
                baseRotX + microRotX,
                baseRotY + microRotY,
                baseRotZ
            ));
            
            headRotationValues.push(
                quaternion.x, quaternion.y, quaternion.z, quaternion.w
            );
        }
        
        const headRotation = new THREE.QuaternionKeyframeTrack(
            'head.quaternion',
            headRotationTimes,
            headRotationValues
        );
        
        tracks.push(headRotation);
        
        const talkingClip = new THREE.AnimationClip('talking', duration, tracks);
        this.animationActions.talking = this.mixer.clipAction(talkingClip);
        this.animationActions.talking.setLoop(THREE.LoopRepeat);
    }

    createProfessionalNoteWritingAnimation() {
        const tracks = [];
        const duration = 3.0;
        
        // Right arm writing motion
        const times = [0, 1, 2, 3];
        const rightArmValues = [];
        
        times.forEach(t => {
            const quaternion = new THREE.Quaternion();
            quaternion.setFromEuler(new THREE.Euler(
                Math.PI / 4 + Math.sin(t * 2) * 0.1,
                0,
                -Math.PI / 6 + Math.sin(t * 3) * 0.2
            ));
            rightArmValues.push(quaternion.x, quaternion.y, quaternion.z, quaternion.w);
        });
        
        const rightArmTrack = new THREE.QuaternionKeyframeTrack(
            'rightArmGroup.quaternion',
            times,
            rightArmValues
        );
        
        tracks.push(rightArmTrack);
        
        const noteWritingClip = new THREE.AnimationClip('noteWriting', duration, tracks);
        this.animationActions.noteWriting = this.mixer.clipAction(noteWritingClip);
        this.animationActions.noteWriting.setLoop(THREE.LoopRepeat);
    }

    createNaturalIdleAnimation() {
        const tracks = [];
        const duration = 5.0;
        
        // Subtle breathing animation
        const breathingTimes = [0, 2.5, 5.0];
        const breathingValues = [
            0, 0.8, 0,  // Start
            0, 0.82, 0, // Peak
            0, 0.8, 0   // End
        ];
        
        const breathingTrack = new THREE.VectorKeyframeTrack(
            'torso.position',
            breathingTimes,
            breathingValues
        );
        
        tracks.push(breathingTrack);
        
        const idleClip = new THREE.AnimationClip('idle', duration, tracks);
        this.animationActions.idle = this.mixer.clipAction(idleClip);
        this.animationActions.idle.setLoop(THREE.LoopRepeat);
    }

    createAdvancedThinkingAnimation() {
        const tracks = [];
        const duration = 2.0;
        
        // Head tilting while thinking
        const times = [0, 1, 2];
        const headValues = [];
        
        times.forEach(t => {
            const quaternion = new THREE.Quaternion();
            quaternion.setFromEuler(new THREE.Euler(
                0,
                0,
                Math.sin(t * Math.PI) * 0.1
            ));
            headValues.push(quaternion.x, quaternion.y, quaternion.z, quaternion.w);
        });
        
        const headTrack = new THREE.QuaternionKeyframeTrack(
            'head.quaternion',
            times,
            headValues
        );
        
        tracks.push(headTrack);
        
        const thinkingClip = new THREE.AnimationClip('thinking', duration, tracks);
        this.animationActions.thinking = this.mixer.clipAction(thinkingClip);
        this.animationActions.thinking.setLoop(THREE.LoopRepeat);
    }

    createProfessionalGestureAnimations() {
        // Placeholder for gesture animations
        console.log('Professional gesture animations created');
    }

    setupInteractivity() {
        // Mouse controls for camera
        this.setupMouseControls();
    }

    setupMouseControls() {
        let isMouseDown = false;
        let mouseX = 0;
        let mouseY = 0;
        
        this.renderer.domElement.addEventListener('mousedown', (e) => {
            isMouseDown = true;
            mouseX = e.clientX;
            mouseY = e.clientY;
        });
        
        this.renderer.domElement.addEventListener('mousemove', (e) => {
            if (!isMouseDown) return;
            
            const deltaX = e.clientX - mouseX;
            const deltaY = e.clientY - mouseY;
            
            this.camera.position.x += deltaX * 0.01;
            this.camera.position.y -= deltaY * 0.01;
            this.camera.lookAt(0, 1.6, 0);
            
            mouseX = e.clientX;
            mouseY = e.clientY;
        });
        
        document.addEventListener('mouseup', () => {
            isMouseDown = false;
        });
    }

    createEnhancedUIElements() {
        // UI elements are handled by the external JS file
        console.log('Enhanced UI elements ready');
    }

    // Public methods for controlling the avatar
    startTalking() {
        this.stopAllAnimations();
        if (this.animationActions.talking) {
            this.animationActions.talking.play();
            this.currentExpression = 'talking';
        }
    }

    startNoteWriting() {
        this.stopAllAnimations();
        if (this.animationActions.noteWriting) {
            this.animationActions.noteWriting.play();
            this.currentExpression = 'noteWriting';
        }
    }

    setIdle() {
        this.stopAllAnimations();
        if (this.animationActions.idle) {
            this.animationActions.idle.play();
            this.currentExpression = 'idle';
        }
    }

    startThinking() {
        this.stopAllAnimations();
        if (this.animationActions.thinking) {
            this.animationActions.thinking.play();
            this.currentExpression = 'thinking';
        }
    }

    stopAllAnimations() {
        Object.values(this.animationActions).forEach(action => {
            if (action) action.stop();
        });
    }

    animate() {
        requestAnimationFrame(() => this.animate());
        
        const delta = this.clock.getDelta();
        
        if (this.mixer) {
            this.mixer.update(delta);
        }
        
        // Enhanced auto-blink with realistic timing
        this.blinkTimer += delta;
        const blinkInterval = 2.0 + Math.random() * 3.5; // More natural blink timing
        if (this.blinkTimer > blinkInterval) {
            this.performRealisticBlink();
            this.blinkTimer = 0;
        }
        
        // Enhanced breathing animation
        if (this.currentExpression === 'idle' && this.doctorModel) {
            const breathingIntensity = 0.006;
            const breathingSpeed = 0.8;
            const breathing = Math.sin(this.clock.elapsedTime * breathingSpeed) * breathingIntensity;
            
            if (this.torso) {
                this.torso.scale.y = 1 + breathing;
                this.torso.position.y = 0.8 + breathing * 0.3;
            }
            
            // Subtle chest movement
            if (this.head) {
                this.head.position.y = 1.65 + breathing * 0.2;
            }
        }
        
        // Enhanced eye tracking with micro-movements
        if (this.leftEyeball && this.rightEyeball) {
            const eyeTime = this.clock.elapsedTime;
            const eyeMovementX = Math.sin(eyeTime * 0.25) * 0.012 + Math.sin(eyeTime * 1.2) * 0.003;
            const eyeMovementY = Math.sin(eyeTime * 0.2 + Math.PI / 4) * 0.008 + Math.cos(eyeTime * 0.9) * 0.002;
            
            this.leftEyeball.rotation.y = eyeMovementX;
            this.leftEyeball.rotation.x = eyeMovementY;
            this.rightEyeball.rotation.y = eyeMovementX;
            this.rightEyeball.rotation.x = eyeMovementY;
        }
        
        // Enhanced facial micro-expressions
        this.updateMicroExpressions();
        
        this.renderer.render(this.scene, this.camera);
    }

    performRealisticBlink() {
        if (!this.leftUpperEyelid || !this.rightUpperEyelid) return;
        
        const blinkDuration = 0.10;
        const halfBlink = blinkDuration / 2;
        
        // Store original scales
        const originalScaleY = this.leftUpperEyelid.scale.y;
        
        // Close phase with more natural movement
        const closeAnimation = () => {
            if (this.leftUpperEyelid) this.leftUpperEyelid.scale.y = 0.03;
            if (this.rightUpperEyelid) this.rightUpperEyelid.scale.y = 0.03;
            if (this.leftLowerEyelid) this.leftLowerEyelid.scale.y = 0.03;
            if (this.rightLowerEyelid) this.rightLowerEyelid.scale.y = 0.03;
        };
        
        // Open phase
        const openAnimation = () => {
            if (this.leftUpperEyelid) this.leftUpperEyelid.scale.y = originalScaleY;
            if (this.rightUpperEyelid) this.rightUpperEyelid.scale.y = originalScaleY;
            if (this.leftLowerEyelid) this.leftLowerEyelid.scale.y = originalScaleY;
            if (this.rightLowerEyelid) this.rightLowerEyelid.scale.y = originalScaleY;
        };
        
        closeAnimation();
        setTimeout(openAnimation, halfBlink * 1000);
    }

    updateMicroExpressions() {
        const time = this.clock.elapsedTime;
        
        // Subtle facial muscle movements
        if (this.head) {
            // Micro head movements
            const microTilt = Math.sin(time * 0.08) * 0.002;
            const microNod = Math.sin(time * 0.12) * 0.001;
            this.head.rotation.z = microTilt;
            this.head.rotation.x = microNod;
        }
        
        // Slight jaw tension variation
        if (this.mouthCavity) {
            const jawTension = Math.sin(time * 0.1) * 0.0008;
            this.mouthCavity.position.y = jawTension;
        }
        
        // Subtle nostril movement
        if (this.nose) {
            const nostrilMovement = Math.sin(time * 0.6) * 0.0005;
            this.nose.scale.x = 1 + nostrilMovement;
        }
    }

    onWindowResize() {
        const width = this.container.offsetWidth;
        const height = this.container.offsetHeight;
        
        this.camera.aspect = width / height;
        this.camera.updateProjectionMatrix();
        
        this.renderer.setSize(width, height);
    }

    dispose() {
        if (this.renderer) {
            this.renderer.dispose();
        }
        if (this.mixer) {
            this.mixer.uncacheRoot(this.doctorModel);
        }
    }
}

// Export the enhanced class
window.UltraRealisticDoctorAvatar3D = UltraRealisticDoctorAvatar3D;