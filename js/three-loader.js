import * as THREE from "three";
import { GLTFLoader } from "three/addons/loaders/GLTFLoader.js";

console.log("✅ three-loader.js running with import map");

// Canvas, Scene & Renderer
const canvas = document.getElementById("bantayogCanvas");
const scene = new THREE.Scene();
const renderer = new THREE.WebGLRenderer({ canvas, antialias: true });
renderer.setSize(canvas.clientWidth, canvas.clientHeight);

// ✅ Brighter rendering
renderer.outputEncoding = THREE.sRGBEncoding;
renderer.toneMapping = THREE.ACESFilmicToneMapping;
renderer.toneMappingExposure = 1.8;

// ✅ Enable shadows
renderer.shadowMap.enabled = true;
renderer.shadowMap.type = THREE.PCFSoftShadowMap;

// Camera
const camera = new THREE.PerspectiveCamera(
  45,
  canvas.clientWidth / canvas.clientHeight,
  0.1,
  100
);
camera.position.set(0, 1.5, 10); // Start a little farther back
let zooming = true; // flag to allow zoom-in animation

// Responsive Resize
window.addEventListener("resize", () => {
  renderer.setSize(canvas.clientWidth, canvas.clientHeight);
  camera.aspect = canvas.clientWidth / canvas.clientHeight;
  camera.updateProjectionMatrix();
});

// 🌟 Lights
const ambientLight = new THREE.AmbientLight(0xffffff, 2);
scene.add(ambientLight);

const dirLight1 = new THREE.DirectionalLight(0xffffff, 2.5);
dirLight1.position.set(5, 10, 7.5);
dirLight1.castShadow = true;
scene.add(dirLight1);

const dirLight2 = new THREE.DirectionalLight(0xffffff, 1.5);
dirLight2.position.set(-5, 5, -5);
scene.add(dirLight2);

const hemiLight = new THREE.HemisphereLight(0xfff5e6, 0x333333, 2.0);
hemiLight.position.set(0, 20, 0);
scene.add(hemiLight);

const spotLight = new THREE.SpotLight(0xffffff, 4);
spotLight.position.set(0, 8, 10);
spotLight.angle = Math.PI / 6;
spotLight.penumbra = 0.4;
spotLight.decay = 2;
spotLight.distance = 100;
spotLight.castShadow = true;
scene.add(spotLight);

// 🌫️ Fog
scene.fog = new THREE.FogExp2(0x111111, 0.05);

// ==================
// Ground & Glow Plane
// ==================
const groundGeometry = new THREE.PlaneGeometry(50, 50);
const groundMaterial = new THREE.ShadowMaterial({ opacity: 0.3 });
const ground = new THREE.Mesh(groundGeometry, groundMaterial);
ground.rotation.x = -Math.PI / 2;
ground.position.y = 0;
ground.receiveShadow = true;
scene.add(ground);

function createRadialGradientTexture() {
  const size = 256;
  const canvas = document.createElement("canvas");
  canvas.width = canvas.height = size;
  const ctx = canvas.getContext("2d");

  const gradient = ctx.createRadialGradient(
    size / 2, size / 2, 10,
    size / 2, size / 2, size / 2
  );
  gradient.addColorStop(0, "rgba(253, 126, 20, 0.4)");
  gradient.addColorStop(1, "rgba(0, 0, 0, 0)");

  ctx.fillStyle = gradient;
  ctx.fillRect(0, 0, size, size);

  return new THREE.CanvasTexture(canvas);
}

const glowGeo = new THREE.CircleGeometry(10, 64);
const glowMat = new THREE.MeshBasicMaterial({
  map: createRadialGradientTexture(),
  transparent: true,
  depthWrite: false,
  opacity: 0.3
});
const glowPlane = new THREE.Mesh(glowGeo, glowMat);
glowPlane.rotation.x = -Math.PI / 2;
glowPlane.position.y = 0.02;
scene.add(glowPlane);

// Loader
const loader = new GLTFLoader();
const MODEL_URL = "assets/3d/bantayog.glb";
let model;

loader.load(
  MODEL_URL,
  (gltf) => {
    console.log("✅ Model loaded:", gltf);

    model = gltf.scene;
    model.scale.set(0.15, 0.15, 0.15);
    model.position.set(0, 0.5, 0);

    model.traverse((child) => {
      if (child.isMesh) {
        child.castShadow = true;
        child.receiveShadow = true;
      }
    });

    scene.add(model);
    hideSplash();
  },
  undefined,
  (error) => {
    console.error("❌ Error loading GLB:", error);
    setTimeout(hideSplash, 4000);
  }
);

// ==================
// Animation Loop
// ==================
let time = 0;

function animate() {
  requestAnimationFrame(animate);
  time += 0.02;

  // Pulsating glow effect
  if (glowMat && glowPlane) {
    glowMat.opacity = 0.25 + Math.sin(time) * 0.1;
    const scale = 1 + Math.sin(time) * 0.05;
    glowPlane.scale.set(scale, scale, scale);
  }

  // Spin model
  if (model) model.rotation.y += 0.01;

  // Cinematic zoom-in
  if (zooming && camera.position.z > 8) {
    camera.position.z -= 0.01; // slow zoom
  }

  renderer.render(scene, camera);
}
animate();

// ==================
// Splash Screen Hide
// ==================
function hideSplash() {
  console.log("🔥 Hiding splash now...");
  const splash = document.getElementById("splash-screen");
  if (splash) {
    setTimeout(() => {
      splash.style.opacity = "0";
      setTimeout(() => {
        splash.remove();
        zooming = false; // stop zoom after splash disappears
      }, 800);
    }, 1000);
  }
}

// Safety timeout
window.addEventListener("load", () => {
  setTimeout(hideSplash, 4000);
});
