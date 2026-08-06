/**
 * PH 3D Map - Simplified 4-Region Interactive 3D Map
 * Uses Three.js (ES Module via importmap) with orbit controls, extrusion, and hover/click interactions
 */

import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

// ============================================================
// 1. REGION POLYGON DATA (simplified normalized coordinates 0-1)
//    These form a rough Philippine shape with 4 regions
// ============================================================
const REGION_POLYGONS = {
    nc: {  // N & C Luzon (top portion)
        name: 'N & C LUZON',
        color: 0x059669,
        points: [
            [0.30, 0.02], [0.35, 0.01], [0.42, 0.02], [0.48, 0.03],
            [0.52, 0.04], [0.55, 0.06], [0.56, 0.08], [0.55, 0.10],
            [0.53, 0.12], [0.50, 0.14], [0.48, 0.16], [0.45, 0.18],
            [0.42, 0.20], [0.38, 0.22], [0.35, 0.24], [0.32, 0.25],
            [0.28, 0.24], [0.25, 0.22], [0.22, 0.20], [0.20, 0.18],
            [0.19, 0.16], [0.20, 0.14], [0.22, 0.12], [0.24, 0.10],
            [0.26, 0.08], [0.28, 0.06], [0.29, 0.04]
        ]
    },
    south: {  // South Luzon (middle-upper)
        name: 'SOUTH LUZON',
        color: 0x10b981,
        points: [
            [0.28, 0.24], [0.32, 0.25], [0.35, 0.24], [0.38, 0.22],
            [0.42, 0.20], [0.45, 0.18], [0.48, 0.20], [0.50, 0.22],
            [0.52, 0.24], [0.53, 0.26], [0.52, 0.28], [0.50, 0.30],
            [0.48, 0.32], [0.45, 0.34], [0.42, 0.36], [0.38, 0.37],
            [0.35, 0.36], [0.32, 0.35], [0.30, 0.34], [0.28, 0.33],
            [0.26, 0.32], [0.24, 0.30], [0.23, 0.28], [0.25, 0.26]
        ]
    },
    vis: {  // Visayas (middle-lower)
        name: 'VISAYAS',
        color: 0x34d399,
        points: [
            [0.18, 0.36], [0.22, 0.35], [0.26, 0.36], [0.30, 0.37],
            [0.34, 0.38], [0.38, 0.39], [0.42, 0.40], [0.46, 0.41],
            [0.50, 0.42], [0.52, 0.44], [0.50, 0.46], [0.48, 0.48],
            [0.45, 0.50], [0.42, 0.52], [0.38, 0.53], [0.34, 0.52],
            [0.30, 0.51], [0.26, 0.50], [0.22, 0.48], [0.18, 0.46],
            [0.16, 0.44], [0.15, 0.42], [0.16, 0.40], [0.17, 0.38]
        ]
    },
    min: {  // Mindanao (bottom)
        name: 'MINDANAO',
        color: 0x4ade80,
        points: [
            [0.20, 0.50], [0.24, 0.49], [0.28, 0.50], [0.32, 0.51],
            [0.36, 0.52], [0.40, 0.53], [0.44, 0.54], [0.48, 0.55],
            [0.52, 0.56], [0.55, 0.58], [0.56, 0.60], [0.55, 0.62],
            [0.53, 0.64], [0.50, 0.66], [0.47, 0.68], [0.44, 0.70],
            [0.40, 0.72], [0.36, 0.74], [0.32, 0.75], [0.28, 0.74],
            [0.24, 0.72], [0.20, 0.70], [0.18, 0.68], [0.16, 0.66],
            [0.15, 0.64], [0.16, 0.62], [0.17, 0.60], [0.18, 0.58],
            [0.19, 0.56], [0.20, 0.54], [0.20, 0.52]
        ]
    }
};

// ============================================================
// 2. THREE.JS SCENE SETUP
// ============================================================
let scene, camera, renderer, controls;
let regionMeshes = {};
let raycaster, mouse;
let animationId;
let isInitialized = false;
let container;
let hoveredRegion = null;
let selectedRegion = null;
let dataValues = {};
let maxDataValue = 1;
let oceanMesh = null;

// Callbacks
let onHoverCallback = null;
let onClickCallback = null;
let onLeaveCallback = null;

const PH3DMap = {
    /**
     * Initialize the 3D map
     * @param {string|HTMLElement} containerEl - Container element or ID
     * @param {Object} options - Configuration options
     */
    init: function(containerEl, options) {
        if (isInitialized) {
            this.destroy();
        }

        container = typeof containerEl === 'string' 
            ? document.getElementById(containerEl) 
            : containerEl;
        
        if (!container) {
            console.error('PH3DMap: Container not found');
            return;
        }

        const opts = options || {};
        onHoverCallback = opts.onHover || null;
        onClickCallback = opts.onClick || null;
        onLeaveCallback = opts.onLeave || null;

        // Scene
        scene = new THREE.Scene();
        scene.background = new THREE.Color(0xf0f4f8);

        // Camera
        const rect = container.getBoundingClientRect();
        const aspect = rect.width / rect.height;
        camera = new THREE.PerspectiveCamera(40, aspect, 0.1, 100);
        camera.position.set(0.1, 1.0, 2.0);
        camera.lookAt(0, 0, 0);

        // Renderer
        renderer = new THREE.WebGLRenderer({ 
            antialias: true,
            alpha: true
        });
        renderer.setSize(rect.width, rect.height);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.shadowMap.enabled = true;
        renderer.shadowMap.type = THREE.PCFSoftShadowMap;
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.2;
        renderer.outputColorSpace = THREE.SRGBColorSpace;
        container.appendChild(renderer.domElement);

        // Controls
        controls = new OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.08;
        controls.rotateSpeed = 0.4;
        controls.zoomSpeed = 0.7;
        controls.minDistance = 0.8;
        controls.maxDistance = 3.5;
        controls.maxPolarAngle = Math.PI / 2.2;
        controls.minPolarAngle = 0.1;
        controls.target.set(0, 0.05, 0);
        controls.update();

        // Lights
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.4);
        scene.add(ambientLight);

        const dirLight = new THREE.DirectionalLight(0xffffff, 1.2);
        dirLight.position.set(2, 5, 3);
        dirLight.castShadow = true;
        scene.add(dirLight);

        const fillLight = new THREE.DirectionalLight(0x8ecae6, 0.3);
        fillLight.position.set(-2, 1, -1);
        scene.add(fillLight);

        const rimLight = new THREE.DirectionalLight(0xffffff, 0.3);
        rimLight.position.set(0, -1, -2);
        scene.add(rimLight);

        // Ocean/water plane
        const waterGeo = new THREE.CircleGeometry(2.5, 32);
        const waterMat = new THREE.MeshStandardMaterial({
            color: 0xd0e2f2,
            transparent: true,
            opacity: 0.25,
            side: THREE.DoubleSide,
            roughness: 0.8,
            metalness: 0.1
        });
        oceanMesh = new THREE.Mesh(waterGeo, waterMat);
        oceanMesh.rotation.x = -Math.PI / 2;
        oceanMesh.position.y = -0.02;
        oceanMesh.receiveShadow = true;
        scene.add(oceanMesh);

        // Grid helper (subtle)
        const gridHelper = new THREE.GridHelper(3, 12, 0xc8d6e5, 0xe0e8f0);
        gridHelper.position.y = -0.01;
        gridHelper.material.transparent = true;
        gridHelper.material.opacity = 0.2;
        scene.add(gridHelper);

        // Raycaster
        raycaster = new THREE.Raycaster();
        mouse = new THREE.Vector2();

        // Mouse events
        renderer.domElement.addEventListener('mousemove', this._onMouseMove.bind(this));
        renderer.domElement.addEventListener('click', this._onClick.bind(this));
        renderer.domElement.addEventListener('mouseleave', this._onMouseLeave.bind(this));

        // Touch events for mobile
        renderer.domElement.addEventListener('touchstart', this._onTouchStart.bind(this), { passive: true });

        // Resize
        this._resizeHandler = this._onResize.bind(this);
        window.addEventListener('resize', this._resizeHandler);

        isInitialized = true;

        // Build initial regions with default data
        this._buildRegions();

        // Start animation
        this._animate();
    },

    /**
     * Update region data and rebuild meshes
     * @param {Object} data - { regionKey: { value: number, volume: number, cm: number, label: string, ... } }
     */
    updateData: function(data) {
        dataValues = data || {};
        
        // Calculate max value for normalization
        maxDataValue = 1;
        const keys = Object.keys(dataValues);
        if (keys.length > 0) {
            maxDataValue = Math.max(1, ...keys.map(k => {
                const v = dataValues[k];
                return v ? (v.value || v.volume || v.cm || 1) : 1;
            }));
        }

        this._buildRegions();
    },

    /**
     * Build/rebuild region meshes
     */
    _buildRegions: function() {
        // Remove old meshes
        Object.keys(regionMeshes).forEach(key => {
            if (regionMeshes[key]) {
                scene.remove(regionMeshes[key].mesh);
                regionMeshes[key].mesh.geometry.dispose();
                regionMeshes[key].mesh.material.dispose();
                if (regionMeshes[key].outline) {
                    scene.remove(regionMeshes[key].outline);
                    regionMeshes[key].outline.geometry.dispose();
                    regionMeshes[key].outline.material.dispose();
                }
                if (regionMeshes[key].label) {
                    scene.remove(regionMeshes[key].label);
                }
            }
        });
        regionMeshes = {};

        const keys = Object.keys(REGION_POLYGONS);
        
        keys.forEach(key => {
            const region = REGION_POLYGONS[key];
            const data = dataValues[key] || {};
            const rawValue = data.value || data.volume || data.cm || 0;
            const normalizedValue = maxDataValue > 0 ? rawValue / maxDataValue : 0;
            
            // Extrusion height: base 0.04 + up to 0.35 based on value
            const extrudeHeight = 0.04 + (normalizedValue * 0.35);
            
            // Base color from region or data
            const baseColor = data.color || region.color;
            
            // Create shape from points
            const shape = new THREE.Shape();
            const pts = region.points;
            
            // Center the points around origin
            let cx = 0, cy = 0;
            pts.forEach(p => { cx += p[0]; cy += p[1]; });
            cx /= pts.length;
            cy /= pts.length;
            
            // Scale factor to fit nicely
            const scale = 1.6;
            
            shape.moveTo((pts[0][0] - cx) * scale, (pts[0][1] - cy) * scale);
            for (let i = 1; i < pts.length; i++) {
                shape.lineTo((pts[i][0] - cx) * scale, (pts[i][1] - cy) * scale);
            }
            shape.closePath();

            // Extrude settings
            const extrudeSettings = {
                steps: 1,
                depth: extrudeHeight,
                bevelEnabled: true,
                bevelThickness: 0.02,
                bevelSize: 0.015,
                bevelSegments: 3
            };

            const geometry = new THREE.ExtrudeGeometry(shape, extrudeSettings);
            geometry.computeVertexNormals();

            // Color based on performance ratio
            const color = new THREE.Color(baseColor);
            if (normalizedValue > 0.7) {
                color.offsetHSL(0, 0, 0.12);
            } else if (normalizedValue < 0.3 && normalizedValue > 0) {
                color.offsetHSL(0, -0.15, -0.08);
            }

            const material = new THREE.MeshStandardMaterial({
                color: color,
                roughness: 0.35,
                metalness: 0.15,
                emissive: new THREE.Color(baseColor),
                emissiveIntensity: 0.05 + (normalizedValue * 0.12),
                transparent: true,
                opacity: 0.92
            });

            const mesh = new THREE.Mesh(geometry, material);
            mesh.castShadow = true;
            mesh.receiveShadow = true;
            mesh.userData = {
                regionKey: key,
                regionName: region.name,
                data: data
            };
            mesh.position.y = 0;
            scene.add(mesh);

            // Add outline (wireframe edge)
            const edges = new THREE.EdgesGeometry(geometry);
            const lineMat = new THREE.LineBasicMaterial({ 
                color: 0x1e293b, 
                transparent: true, 
                opacity: 0.12 
            });
            const outline = new THREE.LineSegments(edges, lineMat);
            outline.position.copy(mesh.position);
            scene.add(outline);

            // Create sprite label
            const canvas = document.createElement('canvas');
            canvas.width = 256;
            canvas.height = 64;
            const ctx = canvas.getContext('2d');
            
            ctx.fillStyle = 'rgba(15, 23, 42, 0.7)';
            ctx.roundRect(0, 0, 256, 64, 8);
            ctx.fill();
            
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 22px Arial, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            
            // Short label
            const shortLabels = { nc: 'N&C LUZON', south: 'S LUZON', vis: 'VISAYAS', min: 'MINDANAO' };
            ctx.fillText(shortLabels[key] || region.name, 128, 32);
            
            const texture = new THREE.CanvasTexture(canvas);
            texture.minFilter = THREE.LinearFilter;
            
            const spriteMat = new THREE.SpriteMaterial({ 
                map: texture,
                transparent: true,
                depthTest: false,
                sizeAttenuation: true
            });
            const sprite = new THREE.Sprite(spriteMat);
            
            // Position label above the region center
            const centerX = (Math.max(...pts.map(p => p[0])) + Math.min(...pts.map(p => p[0]))) / 2;
            const centerY = (Math.max(...pts.map(p => p[1])) + Math.min(...pts.map(p => p[1]))) / 2;
            sprite.position.set(
                (centerX - cx) * scale,
                extrudeHeight + 0.08,
                (centerY - cy) * scale
            );
            sprite.scale.set(0.6, 0.15, 1);
            scene.add(sprite);

            // Store reference
            regionMeshes[key] = {
                mesh: mesh,
                outline: outline,
                label: sprite,
                baseColor: baseColor,
                defaultHeight: extrudeHeight,
                center: { x: (centerX - cx) * scale, z: (centerY - cy) * scale }
            };
        });
    },

    /**
     * Handle mouse move for hover effects
     */
    _onMouseMove: function(event) {
        if (!isInitialized) return;

        const rect = renderer.domElement.getBoundingClientRect();
        mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
        mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

        raycaster.setFromCamera(mouse, camera);
        
        const meshes = Object.keys(regionMeshes).map(k => regionMeshes[k].mesh);
        const intersects = raycaster.intersectObjects(meshes);

        if (intersects.length > 0) {
            const hit = intersects[0].object;
            const key = hit.userData.regionKey;
            
            if (hoveredRegion !== key) {
                // Reset previous hover
                this._resetHover();
                
                // Set new hover
                hoveredRegion = key;
                const rm = regionMeshes[key];
                if (rm) {
                    rm.mesh.material.emissiveIntensity = 0.45;
                    rm.mesh.material.opacity = 1.0;
                    rm.mesh.scale.set(1.04, 1.04, 1.04);
                    renderer.domElement.style.cursor = 'pointer';
                }
                
                if (onHoverCallback) {
                    onHoverCallback(key, hit.userData);
                }
            }
        } else {
            if (hoveredRegion !== null) {
                this._resetHover();
                hoveredRegion = null;
                renderer.domElement.style.cursor = 'default';
                if (onLeaveCallback) {
                    onLeaveCallback();
                }
            }
        }
    },

    /**
     * Handle touch start (mobile)
     */
    _onTouchStart: function(event) {
        if (!isInitialized || !event.touches.length) return;
        
        const touch = event.touches[0];
        const rect = renderer.domElement.getBoundingClientRect();
        mouse.x = ((touch.clientX - rect.left) / rect.width) * 2 - 1;
        mouse.y = -((touch.clientY - rect.top) / rect.height) * 2 + 1;

        raycaster.setFromCamera(mouse, camera);
        
        const meshes = Object.keys(regionMeshes).map(k => regionMeshes[k].mesh);
        const intersects = raycaster.intersectObjects(meshes);

        if (intersects.length > 0) {
            const hit = intersects[0].object;
            const key = hit.userData.regionKey;
            if (onClickCallback) {
                onClickCallback(key, hit.userData);
            }
        }
    },

    /**
     * Handle click on region
     */
    _onClick: function(event) {
        if (!isInitialized) return;

        const rect = renderer.domElement.getBoundingClientRect();
        mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
        mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

        raycaster.setFromCamera(mouse, camera);
        
        const meshes = Object.keys(regionMeshes).map(k => regionMeshes[k].mesh);
        const intersects = raycaster.intersectObjects(meshes);

        if (intersects.length > 0) {
            const hit = intersects[0].object;
            const key = hit.userData.regionKey;
            
            // Reset previous selection
            if (selectedRegion && regionMeshes[selectedRegion]) {
                const prev = regionMeshes[selectedRegion];
                prev.mesh.material.emissive.setHex(prev.baseColor);
                const prevData = dataValues[selectedRegion] || {};
                const prevVal = prevData.value || prevData.volume || prevData.cm || 0;
                const prevNorm = maxDataValue > 0 ? prevVal / maxDataValue : 0;
                prev.mesh.material.emissiveIntensity = 0.05 + (prevNorm * 0.12);
            }
            
            selectedRegion = key;
            const rm = regionMeshes[key];
            if (rm) {
                rm.mesh.material.emissive.setHex(0xfbbf24);
                rm.mesh.material.emissiveIntensity = 0.35;
            }
            
            if (onClickCallback) {
                onClickCallback(key, hit.userData);
            }
        }
    },

    /**
     * Handle mouse leave
     */
    _onMouseLeave: function() {
        this._resetHover();
        hoveredRegion = null;
        renderer.domElement.style.cursor = 'default';
        if (onLeaveCallback) {
            onLeaveCallback();
        }
    },

    /**
     * Reset hover state
     */
    _resetHover: function() {
        if (hoveredRegion && regionMeshes[hoveredRegion]) {
            const rm = regionMeshes[hoveredRegion];
            const data = dataValues[hoveredRegion] || {};
            const rawValue = data.value || data.volume || data.cm || 0;
            const perfRatio = maxDataValue > 0 ? rawValue / maxDataValue : 0;
            rm.mesh.material.emissiveIntensity = 0.05 + (perfRatio * 0.12);
            rm.mesh.material.opacity = 0.92;
            rm.mesh.scale.set(1, 1, 1);
        }
    },

    /**
     * Animation loop
     */
    _animate: function() {
        if (!isInitialized) return;
        animationId = requestAnimationFrame(this._animate.bind(this));
        controls.update();
        renderer.render(scene, camera);
    },

    /**
     * Handle resize
     */
    _onResize: function() {
        if (!isInitialized || !container) return;
        const rect = container.getBoundingClientRect();
        if (rect.width === 0 || rect.height === 0) return;
        camera.aspect = rect.width / rect.height;
        camera.updateProjectionMatrix();
        renderer.setSize(rect.width, rect.height);
    },

    /**
     * Destroy the 3D scene
     */
    destroy: function() {
        if (animationId) {
            cancelAnimationFrame(animationId);
            animationId = null;
        }
        
        if (renderer) {
            renderer.domElement.removeEventListener('mousemove', this._onMouseMove);
            renderer.domElement.removeEventListener('click', this._onClick);
            renderer.domElement.removeEventListener('mouseleave', this._onMouseLeave);
            renderer.domElement.removeEventListener('touchstart', this._onTouchStart);
            
            if (this._resizeHandler) {
                window.removeEventListener('resize', this._resizeHandler);
            }
            
            if (container && renderer.domElement.parentNode === container) {
                container.removeChild(renderer.domElement);
            }
            renderer.dispose();
        }
        
        // Dispose meshes
        Object.keys(regionMeshes).forEach(key => {
            const rm = regionMeshes[key];
            if (rm.mesh) {
                rm.mesh.geometry.dispose();
                rm.mesh.material.dispose();
            }
            if (rm.outline) {
                rm.outline.geometry.dispose();
                rm.outline.material.dispose();
            }
            if (rm.label) {
                rm.label.material.map.dispose();
                rm.label.material.dispose();
            }
        });
        regionMeshes = {};
        
        // Dispose scene objects
        if (scene) {
            scene.traverse(obj => {
                if (obj.isMesh) {
                    obj.geometry.dispose();
                    if (Array.isArray(obj.material)) {
                        obj.material.forEach(m => m.dispose());
                    } else {
                        obj.material.dispose();
                    }
                }
            });
        }
        
        if (oceanMesh) {
            oceanMesh.geometry.dispose();
            oceanMesh.material.dispose();
            oceanMesh = null;
        }
        
        scene = null;
        camera = null;
        renderer = null;
        controls = null;
        isInitialized = false;
        container = null;
        hoveredRegion = null;
        selectedRegion = null;
        dataValues = {};
    },

    /**
     * Check if initialized
     */
    isReady: function() {
        return isInitialized;
    },

    /**
     * Get region data by key
     */
    getRegion: function(key) {
        return regionMeshes[key] || null;
    },

    /**
     * Get all region keys
     */
    getRegionKeys: function() {
        return Object.keys(REGION_POLYGONS);
    }
};

export { PH3DMap };
export default PH3DMap;