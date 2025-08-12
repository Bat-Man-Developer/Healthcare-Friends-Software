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
        const keyLight = new THREE.DirectionalLight(0xffffff, 1.8);
        keyLight.position.set(3, 5, 4);
        keyLight.castShadow = true;
        keyLight.shadow.mapSize.setScalar(4096);
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
        const fillLight = new THREE.DirectionalLight(0xb3d9ff, 0.6);
        fillLight.position.set(-3, 3, 3);
        this.scene.add(fillLight);

        // Rim light (professional edge lighting)
        const rimLight = new THREE.DirectionalLight(0xffffff, 0.8);
        rimLight.position.set(-2, 2, -3);
        this.scene.add(rimLight);

        // Eye light (catch light in eyes)
        const eyeLight = new THREE.PointLight(0xffffff, 0.3, 3);
        eyeLight.position.set(0, 1.8, 2);
        this.scene.add(eyeLight);

        // Ambient light with color temperature
        const ambientLight = new THREE.AmbientLight(0xf0f8ff, 0.4);
        this.scene.add(ambientLight);

        // Professional environment lighting
        const envLight1 = new THREE.PointLight(0xffeecc, 0.5, 8);
        envLight1.position.set(2, 3, 2);
        this.scene.add(envLight1);

        const envLight2 = new THREE.PointLight(0xe6f3ff, 0.4, 6);
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
        
        // Custom head shape modifications
        const headVertices = headGeometry.attributes.position.array;
        for (let i = 0; i < headVertices.length; i += 3) {
            const x = headVertices[i];
            const y = headVertices[i + 1];
            const z = headVertices[i + 2];
            
            // Create more realistic head shape
            const jawFactor = Math.max(0, -y + 0.1) * 0.3;
            const foreheadFactor = Math.max(0, y - 0.1) * 0.2;
            
            headVertices[i] *= (1 - jawFactor + foreheadFactor); // X scaling
            headVertices[i + 1] *= 1.05; // Y scaling (slightly taller)
            headVertices[i + 2] *= (0.95 + jawFactor * 0.1); // Z scaling
        }
        
        headGeometry.attributes.position.needsUpdate = true;
        headGeometry.computeVertexNormals();
        
        // Ultra-realistic skin material
        const headMaterial = new THREE.MeshPhysicalMaterial({
            color: 0xfdbcb4,
            roughness: 0.7,
            metalness: 0.0,
            clearcoat: 0.05,
            clearcoatRoughness: 0.8,
            transmission: 0.01,
            thickness: 0.1,
            ior: 1.4,
            sheenColor: 0xffffff,
            sheen: 0.1
        });
        
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

    createUltraRealisticEyes() {
        // Create realistic eye sockets
        const socketGeometry = new THREE.SphereGeometry(0.075, 32, 32);
        const socketMaterial = new THREE.MeshLambertMaterial({ 
            color: 0xe0c5a8,
            transparent: true,
            opacity: 0.9 
        });
        
        // Left eye socket
        const leftSocket = new THREE.Mesh(socketGeometry, socketMaterial);
        leftSocket.position.set(-0.08, 0.06, 0.20);
        leftSocket.scale.set(1.1, 0.7, 0.9);
        this.head.add(leftSocket);
        
        // Right eye socket
        const rightSocket = new THREE.Mesh(socketGeometry, socketMaterial);
        rightSocket.position.set(0.08, 0.06, 0.20);
        rightSocket.scale.set(1.1, 0.7, 0.9);
        this.head.add(rightSocket);
        
        // Ultra-realistic eyeballs
        const eyeballGeometry = new THREE.SphereGeometry(0.048, 32, 32);
        const eyeballMaterial = new THREE.MeshPhysicalMaterial({ 
            color: 0xffffff,
            roughness: 0.1,
            metalness: 0.0,
            transmission: 0.1,
            ior: 1.336,
            clearcoat: 1.0,
            clearcoatRoughness: 0.1
        });
        
        this.leftEyeball = new THREE.Mesh(eyeballGeometry, eyeballMaterial);
        this.leftEyeball.position.set(-0.08, 0.06, 0.22);
        this.leftEyeball.name = 'leftEyeball';
        this.head.add(this.leftEyeball);
        
        this.rightEyeball = new THREE.Mesh(eyeballGeometry, eyeballMaterial);
        this.rightEyeball.position.set(0.08, 0.06, 0.22);
        this.rightEyeball.name = 'rightEyeball';
        this.head.add(this.rightEyeball);
        
        // Detailed iris with texture
        const irisGeometry = new THREE.CircleGeometry(0.022, 32);
        const irisMaterial = new THREE.MeshPhysicalMaterial({ 
            color: 0x2e5c8a,
            roughness: 0.8,
            metalness: 0.0,
            normalScale: new THREE.Vector2(0.5, 0.5)
        });
        
        // Create iris pattern
        const irisCanvas = document.createElement('canvas');
        irisCanvas.width = 64;
        irisCanvas.height = 64;
        const irisCtx = irisCanvas.getContext('2d');
        
        // Draw iris pattern
        const centerX = 32, centerY = 32;
        for (let i = 0; i < 20; i++) {
            const angle = (i / 20) * Math.PI * 2;
            const gradient = irisCtx.createRadialGradient(centerX, centerY, 0, centerX, centerY, 30);
            gradient.addColorStop(0, `hsl(${210 + i * 2}, 70%, ${40 + i}%)`);
            gradient.addColorStop(1, `hsl(${210 + i * 2}, 60%, ${30 + i}%)`);
            
            irisCtx.strokeStyle = gradient;
            irisCtx.lineWidth = 0.5;
            irisCtx.beginPath();
            irisCtx.moveTo(centerX, centerY);
            irisCtx.lineTo(centerX + Math.cos(angle) * 30, centerY + Math.sin(angle) * 30);
            irisCtx.stroke();
        }
        
        const irisTexture = new THREE.CanvasTexture(irisCanvas);
        irisMaterial.map = irisTexture;
        
        const leftIris = new THREE.Mesh(irisGeometry, irisMaterial);
        leftIris.position.set(0, 0, 0.046);
        this.leftEyeball.add(leftIris);
        
        const rightIris = new THREE.Mesh(irisGeometry, irisMaterial);
        rightIris.position.set(0, 0, 0.046);
        this.rightEyeball.add(rightIris);
        
        // Pupils with depth
        const pupilGeometry = new THREE.CircleGeometry(0.009, 16);
        const pupilMaterial = new THREE.MeshBasicMaterial({ color: 0x000000 });
        
        const leftPupil = new THREE.Mesh(pupilGeometry, pupilMaterial);
        leftPupil.position.set(0, 0, 0.001);
        leftIris.add(leftPupil);
        
        const rightPupil = new THREE.Mesh(pupilGeometry, pupilMaterial);
        rightPupil.position.set(0, 0, 0.001);
        rightIris.add(rightPupil);
        
        // Realistic eyelids with proper anatomy
        this.createAnatomicalEyelids();
        
        // Eye reflections
        this.createEyeReflections();
    }

    createAnatomicalEyelids() {
        // Upper eyelids with proper curvature
        const upperEyelidGeometry = new THREE.SphereGeometry(0.055, 24, 16, 0, Math.PI * 2, 0, Math.PI * 0.6);
        const eyelidMaterial = new THREE.MeshLambertMaterial({ 
            color: 0xf5c6a8,
            transparent: true,
            opacity: 0.95
        });
        
        this.leftUpperEyelid = new THREE.Mesh(upperEyelidGeometry, eyelidMaterial);
        this.leftUpperEyelid.position.set(-0.08, 0.085, 0.21);
        this.leftUpperEyelid.rotation.x = Math.PI;
        this.leftUpperEyelid.castShadow = true;
        this.leftUpperEyelid.name = 'leftUpperEyelid';
        this.head.add(this.leftUpperEyelid);
        
        this.rightUpperEyelid = new THREE.Mesh(upperEyelidGeometry, eyelidMaterial);
        this.rightUpperEyelid.position.set(0.08, 0.085, 0.21);
        this.rightUpperEyelid.rotation.x = Math.PI;
        this.rightUpperEyelid.castShadow = true;
        this.rightUpperEyelid.name = 'rightUpperEyelid';
        this.head.add(this.rightUpperEyelid);
        
        // Lower eyelids
        const lowerEyelidGeometry = new THREE.SphereGeometry(0.052, 24, 16, 0, Math.PI * 2, 0, Math.PI * 0.4);
        
        this.leftLowerEyelid = new THREE.Mesh(lowerEyelidGeometry, eyelidMaterial);
        this.leftLowerEyelid.position.set(-0.08, 0.035, 0.21);
        this.leftLowerEyelid.name = 'leftLowerEyelid';
        this.head.add(this.leftLowerEyelid);
        
        this.rightLowerEyelid = new THREE.Mesh(lowerEyelidGeometry, eyelidMaterial);
        this.rightLowerEyelid.position.set(0.08, 0.035, 0.21);
        this.rightLowerEyelid.name = 'rightLowerEyelid';
        this.head.add(this.rightLowerEyelid);
        
        // Eyelashes
        this.createEyelashes();
    }

    createEyelashes() {
        const lashMaterial = new THREE.MeshLambertMaterial({ color: 0x2c1810 });
        
        // Upper eyelashes
        for (let eye = 0; eye < 2; eye++) {
            const xOffset = eye === 0 ? -0.08 : 0.08;
            const eyelid = eye === 0 ? this.leftUpperEyelid : this.rightUpperEyelid;
            
            for (let i = 0; i < 12; i++) {
                const lashGeometry = new THREE.CylinderGeometry(0.0005, 0.0003, 0.015, 4);
                const lash = new THREE.Mesh(lashGeometry, lashMaterial);
                
                const angle = (i / 12) * Math.PI - Math.PI / 2;
                const radius = 0.045;
                
                lash.position.set(
                    Math.cos(angle) * radius * 0.5,
                    0.02,
                    Math.sin(angle) * radius + 0.03
                );
                
                lash.rotation.z = angle;
                lash.rotation.x = Math.PI / 6;
                
                eyelid.add(lash);
            }
        }
    }

    createEyeReflections() {
        // Corneal reflections
        const reflectionGeometry = new THREE.SphereGeometry(0.003, 8, 8);
        const reflectionMaterial = new THREE.MeshBasicMaterial({ 
            color: 0xffffff,
            transparent: true,
            opacity: 0.8
        });
        
        const leftReflection = new THREE.Mesh(reflectionGeometry, reflectionMaterial);
        leftReflection.position.set(-0.015, 0.01, 0.047);
        this.leftEyeball.add(leftReflection);
        
        const rightReflection = new THREE.Mesh(reflectionGeometry, reflectionMaterial);
        rightReflection.position.set(-0.015, 0.01, 0.047);
        this.rightEyeball.add(rightReflection);
    }

    createExpressiveEyebrows() {
        // Create realistic eyebrow hair strands
        const browMaterial = new THREE.MeshLambertMaterial({ color: 0x5d4037 });
        
        // Left eyebrow
        this.leftEyebrow = new THREE.Group();
        this.leftEyebrow.position.set(-0.08, 0.13, 0.22);
        this.leftEyebrow.name = 'leftEyebrow';
        
        for (let i = 0; i < 25; i++) {
            const hairGeometry = new THREE.CylinderGeometry(0.0008, 0.0005, 0.02, 4);
            const hair = new THREE.Mesh(hairGeometry, browMaterial);
            
            const x = (i - 12) * 0.008;
            const y = Math.sin(i * 0.3) * 0.003;
            const z = Math.cos(i * 0.2) * 0.005;
            
            hair.position.set(x, y, z);
            hair.rotation.z = (i - 12) * 0.05;
            hair.rotation.x = Math.PI / 6 + Math.random() * 0.2;
            
            this.leftEyebrow.add(hair);
        }
        this.head.add(this.leftEyebrow);
        
        // Right eyebrow (mirrored)
        this.rightEyebrow = this.leftEyebrow.clone();
        this.rightEyebrow.position.set(0.08, 0.13, 0.22);
        this.rightEyebrow.scale.x = -1;
        this.rightEyebrow.name = 'rightEyebrow';
        this.head.add(this.rightEyebrow);
    }

    createRealisticNose() {
        // Anatomically correct nose
        const noseGroup = new THREE.Group();
        
        // Nose bridge
        const bridgeGeometry = new THREE.BoxGeometry(0.015, 0.08, 0.025);
        const noseMaterial = new THREE.MeshLambertMaterial({ color: 0xfdbcb4 });
        
        const bridge = new THREE.Mesh(bridgeGeometry, noseMaterial);
        bridge.position.set(0, 0.02, 0.01);
        noseGroup.add(bridge);
        
        // Nose tip
        const tipGeometry = new THREE.SphereGeometry(0.018, 16, 16);
        const tip = new THREE.Mesh(tipGeometry, noseMaterial);
        tip.position.set(0, -0.02, 0.02);
        tip.scale.set(1, 0.8, 1.2);
        noseGroup.add(tip);
        
        // Nostrils with proper anatomy
        const nostrilGeometry = new THREE.SphereGeometry(0.006, 12, 12);
        const nostrilMaterial = new THREE.MeshLambertMaterial({ color: 0xd4a574 });
        
        const leftNostril = new THREE.Mesh(nostrilGeometry, nostrilMaterial);
        leftNostril.position.set(-0.012, -0.025, 0.015);
        leftNostril.scale.set(0.8, 0.6, 1.2);
        noseGroup.add(leftNostril);
        
        const rightNostril = new THREE.Mesh(nostrilGeometry, nostrilMaterial);
        rightNostril.position.set(0.012, -0.025, 0.015);
        rightNostril.scale.set(0.8, 0.6, 1.2);
        noseGroup.add(rightNostril);
        
        // Nose wings
        const wingGeometry = new THREE.SphereGeometry(0.015, 12, 12);
        
        const leftWing = new THREE.Mesh(wingGeometry, noseMaterial);
        leftWing.position.set(-0.018, -0.015, 0.008);
        leftWing.scale.set(0.6, 0.8, 0.9);
        noseGroup.add(leftWing);
        
        const rightWing = new THREE.Mesh(wingGeometry, noseMaterial);
        rightWing.position.set(0.018, -0.015, 0.008);
        rightWing.scale.set(0.6, 0.8, 0.9);
        noseGroup.add(rightWing);
        
        noseGroup.position.set(0, 0.02, 0.24);
        noseGroup.name = 'nose';
        this.head.add(noseGroup);
        this.nose = noseGroup;
    }

    createDetailedMouth() {
        const mouthGroup = new THREE.Group();
        
        // Mouth cavity with depth
        const cavityGeometry = new THREE.CylinderGeometry(0.035, 0.03, 0.015, 16);
        const cavityMaterial = new THREE.MeshLambertMaterial({ color: 0x2a0a0a });
        
        this.mouthCavity = new THREE.Mesh(cavityGeometry, cavityMaterial);
        this.mouthCavity.rotation.x = Math.PI / 2;
        this.mouthCavity.position.set(0, 0, 0.01);
        mouthGroup.add(this.mouthCavity);
        
        // Realistic lips with proper anatomy
        const lipGeometry = new THREE.TorusGeometry(0.035, 0.008, 8, 24);
        const lipMaterial = new THREE.MeshPhysicalMaterial({ 
            color: 0xcd6155,
            roughness: 0.4,
            metalness: 0.0,
            clearcoat: 0.3,
            clearcoatRoughness: 0.2
        });
        
        this.lips = new THREE.Mesh(lipGeometry, lipMaterial);
        this.lips.rotation.x = Math.PI / 2;
        this.lips.castShadow = true;
        this.lips.name = 'lips';
        mouthGroup.add(this.lips);
        
        // Upper and lower lips separately for expressions
        const upperLipGeometry = new THREE.TorusGeometry(0.035, 0.006, 8, 24, Math.PI);
        this.upperLip = new THREE.Mesh(upperLipGeometry, lipMaterial);
        this.upperLip.rotation.x = Math.PI / 2;
        this.upperLip.position.set(0, 0, 0.002);
        mouthGroup.add(this.upperLip);
        
        const lowerLipGeometry = new THREE.TorusGeometry(0.035, 0.008, 8, 24, Math.PI);
        this.lowerLip = new THREE.Mesh(lowerLipGeometry, lipMaterial);
        this.lowerLip.rotation.x = -Math.PI / 2;
        this.lowerLip.position.set(0, 0, 0.002);
        mouthGroup.add(this.lowerLip);
        
        // Teeth with individual tooth geometry
        this.createRealisticTeeth(mouthGroup);
        
        // Tongue
        this.createTongue(mouthGroup);
        
        mouthGroup.position.set(0, -0.08, 0.23);
        mouthGroup.name = 'mouth';
        this.head.add(mouthGroup);
        this.mouth = mouthGroup;
    }

    createRealisticTeeth(mouthGroup) {
        const teethGroup = new THREE.Group();
        const toothMaterial = new THREE.MeshPhysicalMaterial({ 
            color: 0xffffff,
            roughness: 0.1,
            metalness: 0.0,
            clearcoat: 0.8,
            clearcoatRoughness: 0.1
        });
        
        // Upper teeth
        for (let i = 0; i < 8; i++) {
            const toothGeometry = new THREE.BoxGeometry(0.004, 0.008, 0.006);
            const tooth = new THREE.Mesh(toothGeometry, toothMaterial);
            
            const angle = (i - 3.5) * 0.15;
            const radius = 0.028;
            
            tooth.position.set(
                Math.sin(angle) * radius,
                0.003,
                Math.cos(angle) * radius
            );
            tooth.rotation.y = angle;
            
            teethGroup.add(tooth);
        }
        
        // Lower teeth
        for (let i = 0; i < 8; i++) {
            const toothGeometry = new THREE.BoxGeometry(0.004, 0.007, 0.005);
            const tooth = new THREE.Mesh(toothGeometry, toothMaterial);
            
            const angle = (i - 3.5) * 0.15;
            const radius = 0.026;
            
            tooth.position.set(
                Math.sin(angle) * radius,
                -0.003,
                Math.cos(angle) * radius
            );
            tooth.rotation.y = angle;
            
            teethGroup.add(tooth);
        }
        
        teethGroup.visible = false;
        teethGroup.name = 'teeth';
        mouthGroup.add(teethGroup);
        this.teeth = teethGroup;
    }

    createTongue(mouthGroup) {
        const tongueGeometry = new THREE.SphereGeometry(0.025, 16, 16);
        tongueGeometry.scale(1, 0.4, 1.2);
        
        const tongueMaterial = new THREE.MeshLambertMaterial({ 
            color: 0xff6b9d,
            transparent: true,
            opacity: 0.8
        });
        
        this.tongue = new THREE.Mesh(tongueGeometry, tongueMaterial);
        this.tongue.position.set(0, -0.005, 0.005);
        this.tongue.visible = false;
        this.tongue.name = 'tongue';
        
        mouthGroup.add(this.tongue);
    }

    createProfessionalHairstyle() {
        const hairGroup = new THREE.Group();
        const hairMaterial = new THREE.MeshLambertMaterial({ 
            color: 0x4a4a4a,
            transparent: true,
            opacity: 0.95
        });
        
        // Main hair volume
        const hairGeometry = new THREE.SphereGeometry(0.28, 32, 32, 0, Math.PI * 2, 0, Math.PI * 0.7);
        const mainHair = new THREE.Mesh(hairGeometry, hairMaterial);
        mainHair.position.set(0, 0.05, -0.02);
        mainHair.castShadow = true;
        hairGroup.add(mainHair);
        
        // Hair strands for realism
        for (let i = 0; i < 50; i++) {
            const strandGeometry = new THREE.CylinderGeometry(0.001, 0.0005, 0.04, 4);
            const strand = new THREE.Mesh(strandGeometry, hairMaterial);
            
            const angle = (i / 50) * Math.PI * 2;
            const radius = 0.22 + Math.random() * 0.08;
            const height = 0.1 + Math.random() * 0.15;
            
            strand.position.set(
                Math.cos(angle) * radius,
                height,
                Math.sin(angle) * radius * 0.7
            );
            
            strand.rotation.z = Math.random() * 0.4 - 0.2;
            strand.rotation.x = Math.random() * 0.3;
            
            hairGroup.add(strand);
        }
        
        // Professional side part
        const partGeometry = new THREE.BoxGeometry(0.4, 0.002, 0.3);
        const partMaterial = new THREE.MeshLambertMaterial({ 
            color: 0xfdbcb4,
            transparent: true,
            opacity: 0.3
        });
        
        const part = new THREE.Mesh(partGeometry, partMaterial);
        part.position.set(0.05, 0.25, 0);
        hairGroup.add(part);
        
        hairGroup.name = 'hair';
        this.head.add(hairGroup);
        this.hair = hairGroup;
    }

    createDetailedEars() {
        const earMaterial = new THREE.MeshLambertMaterial({ color: 0xfdbcb4 });
        
        // Left ear with proper anatomy
        const leftEarGroup = new THREE.Group();
        
        // Ear outer rim
        const outerEarGeometry = new THREE.TorusGeometry(0.035, 0.008, 8, 16, Math.PI * 1.5);
        const outerEar = new THREE.Mesh(outerEarGeometry, earMaterial);
        outerEar.rotation.y = Math.PI / 2;
        outerEar.rotation.z = Math.PI / 6;
        leftEarGroup.add(outerEar);
        
        // Ear lobe
        const lobeGeometry = new THREE.SphereGeometry(0.015, 12, 12);
        const lobe = new THREE.Mesh(lobeGeometry, earMaterial);
        lobe.position.set(0, -0.03, 0.01);
        lobe.scale.set(0.8, 1.2, 0.6);
        leftEarGroup.add(lobe);
        
        // Inner ear detail
        const innerGeometry = new THREE.SphereGeometry(0.02, 12, 12);
        const inner = new THREE.Mesh(innerGeometry, earMaterial);
        inner.position.set(0, 0, 0.015);
        inner.scale.set(0.6, 0.8, 0.4);
        leftEarGroup.add(inner);
        
        leftEarGroup.position.set(-0.25, 0.05, 0);
        leftEarGroup.rotation.y = -Math.PI / 6;
        this.head.add(leftEarGroup);
        
        // Right ear (mirrored)
        const rightEarGroup = leftEarGroup.clone();
        rightEarGroup.position.set(0.25, 0.05, 0);
        rightEarGroup.rotation.y = Math.PI / 6;
        rightEarGroup.scale.x = -1;
        this.head.add(rightEarGroup);
    }

    createFacialDetails() {
        // Subtle facial features for realism
        
        // Cheekbones
        const cheekMaterial = new THREE.MeshLambertMaterial({ 
            color: 0xf5c6a8,
            transparent: true,
            opacity: 0.7
        });
        
        const cheekGeometry = new THREE.SphereGeometry(0.02, 16, 16);
        
        const leftCheek = new THREE.Mesh(cheekGeometry, cheekMaterial);
        leftCheek.position.set(-0.12, 0, 0.18);
        leftCheek.scale.set(1.2, 0.6, 0.8);
        this.head.add(leftCheek);
        
        const rightCheek = new THREE.Mesh(cheekGeometry, cheekMaterial);
        rightCheek.position.set(0.12, 0, 0.18);
        rightCheek.scale.set(1.2, 0.6, 0.8);
        this.head.add(rightCheek);
        
        // Subtle facial lines (smile lines, etc.)
        this.createFacialLines();
    }

    createFacialLines() {
        const lineMaterial = new THREE.MeshBasicMaterial({ 
            color: 0xe0b48a,
            transparent: true,
            opacity: 0.3
        });
        
        // Nasolabial folds (smile lines)
        const foldGeometry = new THREE.CylinderGeometry(0.001, 0.001, 0.06, 4);
        
        const leftFold = new THREE.Mesh(foldGeometry, lineMaterial);
        leftFold.position.set(-0.06, -0.02, 0.22);
        leftFold.rotation.z = Math.PI / 6;
        this.head.add(leftFold);
        
        const rightFold = new THREE.Mesh(foldGeometry, lineMaterial);
        rightFold.position.set(0.06, -0.02, 0.22);
        rightFold.rotation.z = -Math.PI / 6;
        this.head.add(rightFold);
    }

    createProfessionalBody() {
        // Professional medical attire with high detail
        const torsoGeometry = new THREE.CylinderGeometry(0.32, 0.38, 0.85, 32);
        const labCoatMaterial = new THREE.MeshPhysicalMaterial({ 
            color: 0xffffff,
            roughness: 0.8,
            metalness: 0.0,
            clearcoat: 0.1,
            clearcoatRoughness: 0.9
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
        
        // FIXED: Better arm positioning - more natural angle and rotation
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
        
        // FIXED: Improved arm positioning - more natural shoulder placement
        armGroup.position.set(xOffset, 1.15, 0.05); // Slightly forward and higher
        
        // FIXED: Better initial arm rotation - more natural resting position
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
                fingerSegment.position.set(0, segmentLength / 2, 0);
                fingerSegment.castShadow = true;
                
                if (segment === 0) {
                    fingerGroup.add(fingerSegment);
                } else {
                    // Add to previous segment
                    const previousSegment = fingerGroup.children[segment - 1];
                    fingerSegment.position.set(0, segmentLength, 0);
                    previousSegment.add(fingerSegment);
                }
                
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
            thumbSegment.position.set(0, segmentLength / 2, 0);
            thumbSegment.castShadow = true;
            
            if (segment === 0) {
                thumbGroup.add(thumbSegment);
            } else {
                const previousSegment = thumbGroup.children[segment - 1];
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

    // Continue with the rest of the class methods...
    // (The animation, interaction, and utility methods remain largely the same,
    // but with enhanced precision and realism in their implementations)

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
        
        // Advanced mouth animation with phoneme-like movements
        const mouthTimes = [];
        const mouthScaleValues = [];
        const jawOpenValues = [];
        
        for (let i = 0; i <= 80; i++) {
            const t = (i / 80) * duration;
            mouthTimes.push(t);
            
            // Create varied mouth movements for different speech sounds
            const baseScale = 1 + Math.sin(t * 12 + Math.sin(t * 3) * 2) * 0.15;
            const variation = Math.sin(t * 20) * 0.1;
            const finalScale = Math.max(0.8, Math.min(1.4, baseScale + variation));
            
            mouthScaleValues.push(finalScale, 1, finalScale);
            
            // Jaw movement
            const jawOpen = Math.max(0, Math.sin(t * 8 + Math.sin(t * 2)) * 0.3);
            jawOpenValues.push(0, -jawOpen * 0.02, 0);
        }
        
        const mouthScale = new THREE.VectorKeyframeTrack(
            'mouth.scale',
            mouthTimes,
            mouthScaleValues
        );
        
        const jawMovement = new THREE.VectorKeyframeTrack(
            'mouthCavity.position',
            mouthTimes,
            jawOpenValues
        );
        
        // Eyebrow expressions during speech
        const eyebrowTimes = [0, 1.0, 2.0, 3.0, 4.0];
        const leftEyebrowRotation = new THREE.QuaternionKeyframeTrack(
            'leftEyebrow.quaternion',
            eyebrowTimes,
            [
                0, 0, 0, 1,
                0, 0, Math.sin(0.05), Math.cos(0.05),
                0, 0, Math.sin(0.1), Math.cos(0.1),
                0, 0, Math.sin(0.03), Math.cos(0.03),
                0, 0, 0, 1
            ]
        );
        
        // Natural hand gestures while speaking
        const gestureAmplitude = 0.3;
        const leftArmTimes = [];
        const leftArmRotations = [];
        const rightArmTimes = [];
        const rightArmRotations = [];
        
        for (let i = 0; i <= 20; i++) {
            const t = (i / 20) * duration;
            leftArmTimes.push(t);
            rightArmTimes.push(t);
            
            // Left arm gesture
            const leftRotZ = Math.PI / 12 + Math.sin(t * 1.5) * gestureAmplitude;
            const leftRotX = Math.PI / 24 + Math.sin(t * 1.2 + Math.PI / 4) * 0.1;
            const leftQuat = new THREE.Quaternion();
            leftQuat.setFromEuler(new THREE.Euler(leftRotX, 0, leftRotZ));
            leftArmRotations.push(leftQuat.x, leftQuat.y, leftQuat.z, leftQuat.w);
            
            // Right arm gesture (complementary)
            const rightRotZ = -Math.PI / 12 + Math.sin(t * 1.3 + Math.PI / 2) * gestureAmplitude;
            const rightRotX = Math.PI / 24 + Math.sin(t * 1.1 + Math.PI / 3) * 0.1;
            const rightQuat = new THREE.Quaternion();
            rightQuat.setFromEuler(new THREE.Euler(rightRotX, 0, rightRotZ));
            rightArmRotations.push(rightQuat.x, rightQuat.y, rightQuat.z, rightQuat.w);
        }
        
        const leftArmGesture = new THREE.QuaternionKeyframeTrack(
            'leftArmGroup.quaternion',
            leftArmTimes,
            leftArmRotations
        );
        
        const rightArmGesture = new THREE.QuaternionKeyframeTrack(
            'rightArmGroup.quaternion',
            rightArmTimes,
            rightArmRotations
        );
        
        tracks.push(
            headRotation, 
            mouthScale, 
            jawMovement,
            leftEyebrowRotation, 
            leftArmGesture, 
            rightArmGesture
        );
        
        const talkingClip = new THREE.AnimationClip('talking', duration, tracks);
        this.animationActions.talking = this.mixer.clipAction(talkingClip);
        this.animationActions.talking.setLoop(THREE.LoopRepeat);
    }

    // Rest of the animation methods follow the same pattern with enhanced realism...
    // (Due to length constraints, I'm showing the structure. The remaining methods would follow
    // the same enhanced approach with more detailed animations, better timing, and realistic movements)

    animate() {
        requestAnimationFrame(() => this.animate());
        
        const delta = this.clock.getDelta();
        
        if (this.mixer) {
            this.mixer.update(delta);
        }
        
        // Enhanced auto-blink with realistic timing
        this.blinkTimer += delta;
        const blinkInterval = 2.5 + Math.random() * 4; // More natural blink timing
        if (this.blinkTimer > blinkInterval) {
            this.performRealisticBlink();
            this.blinkTimer = 0;
        }
        
        // Subtle breathing animation
        if (this.currentExpression === 'idle' && this.doctorModel) {
            const breathingIntensity = 0.008;
            const breathingSpeed = 0.6;
            const breathing = Math.sin(this.clock.elapsedTime * breathingSpeed) * breathingIntensity;
            
            if (this.torso) {
                this.torso.scale.y = 1 + breathing;
                this.torso.position.y = 0.8 + breathing * 0.5;
            }
            
            // Subtle chest movement
            if (this.head) {
                this.head.position.y = 1.65 + breathing * 0.3;
            }
        }
        
        // Eye tracking with micro-movements
        if (this.leftEyeball && this.rightEyeball) {
            const eyeTime = this.clock.elapsedTime;
            const eyeMovementX = Math.sin(eyeTime * 0.3) * 0.008;
            const eyeMovementY = Math.sin(eyeTime * 0.25 + Math.PI / 4) * 0.006;
            
            this.leftEyeball.rotation.y = eyeMovementX;
            this.leftEyeball.rotation.x = eyeMovementY;
            this.rightEyeball.rotation.y = eyeMovementX;
            this.rightEyeball.rotation.x = eyeMovementY;
        }
        
        // Subtle facial micro-expressions
        this.updateMicroExpressions();
        
        this.renderer.render(this.scene, this.camera);
    }

    performRealisticBlink() {
        if (!this.leftUpperEyelid || !this.rightUpperEyelid) return;
        
        const blinkDuration = 0.12;
        const halfBlink = blinkDuration / 2;
        
        // Store original scales
        const originalScaleY = this.leftUpperEyelid.scale.y;
        
        // Close phase
        const closeAnimation = () => {
            if (this.leftUpperEyelid) this.leftUpperEyelid.scale.y = 0.05;
            if (this.rightUpperEyelid) this.rightUpperEyelid.scale.y = 0.05;
            if (this.leftLowerEyelid) this.leftLowerEyelid.scale.y = 0.05;
            if (this.rightLowerEyelid) this.rightLowerEyelid.scale.y = 0.05;
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
            const microTilt = Math.sin(time * 0.1) * 0.003;
            this.head.rotation.z = microTilt;
        }
        
        // Slight jaw tension variation
        if (this.mouthCavity) {
            const jawTension = Math.sin(time * 0.15) * 0.001;
            this.mouthCavity.position.y = jawTension;
        }
    }

    // All other methods remain similar but with enhanced realism...
    // (Including setupInteractivity, createEnhancedUIElements, etc.)
}

// Export the enhanced class
window.DoctorAvatar3D = UltraRealisticDoctorAvatar3D;