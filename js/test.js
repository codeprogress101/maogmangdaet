console.log("✅ test.js is running");
console.log("✅ three.js script loaded");


// DOM Elements
const splash = document.getElementById("splash-screen");
const canvas = document.getElementById("bantayogCanvas");

// Scene & Renderer
const scene = new THREE.Scene();
const renderer = new THREE.WebGLRenderer({
  canvas,
  alpha: true,
  antialias: true,
});
renderer.setPixelRatio(window.devicePixelRatio);
renderer.setSize(canvas.clientWidth, canvas.clientHeight);

// Camera
const camera = new THREE.PerspectiveCamera(
  45,
  canvas.clientWidth / canvas.clientHeight,
  0.1,
  100
);
camera.position.set(0, 1, 3);

// Handle Resize
window.addEventListener("resize", () => {
  renderer.setSize(canvas.clientWidth, canvas.clientHeight);
  camera.aspect = canvas.clientWidth / canvas.clientHeight;
  camera.updateProjectionMatrix();
});

// Lights
scene.add(new THREE.AmbientLight(0xffffff, 2));
const dir = new THREE.DirectionalLight(0xffffff, 2);
dir.position.set(5, 10, 7);
scene.add(dir);

// ✅ Debug Cube (to confirm rendering works)
const testGeo = new THREE.BoxGeometry(1, 1, 1);
const testMat = new THREE.MeshStandardMaterial({ color: 0xfd7e14 });
const cube = new THREE.Mesh(testGeo, testMat);
scene.add(cube);

// Load GLB Model
const loader = new GLTFLoader();
const MODEL_URL = "assets/3d/bantayog.glb"; // ✅ renamed file path
let model;

console.log("Trying to load:", MODEL_URL);

loader.load(
  MODEL_URL,
  (gltf) => {
    console.log("Model loaded:", gltf);
    model = gltf.scene;
    model.scale.set(1.3, 1.3, 1.3);
    scene.add(model);
  },
  undefined,
  (err) => {
    console.error("GLB load error:", err);
    setTimeout(hideSplash, 2000);
  }
);

// Animation Loop
function animate() {
  requestAnimationFrame(animate);

  cube.rotation.y += 0.01; // ✅ always visible
  if (model) model.rotation.y += 0.01;

  renderer.render(scene, camera);
}
animate();

// Hide Splash
function hideSplash() {
  if (splash) splash.style.display = "none";
}
window.addEventListener("load", () => {
  setTimeout(hideSplash, 3000);
});
