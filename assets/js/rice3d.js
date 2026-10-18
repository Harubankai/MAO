// Exploded 3D view of a rice plant (three.js r128). Drag to rotate; button assembles/explodes.
(function () {
  var box = document.getElementById('scene');
  if (!box || !window.THREE) return;
  var T = THREE, reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var renderer;
  try { renderer = new T.WebGLRenderer({ antialias: true, alpha: true }); } catch (err) { return; }
  renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
  box.insertBefore(renderer.domElement, box.firstChild);

  var scene = new T.Scene(), cam = new T.PerspectiveCamera(38, 1, 0.1, 100);
  scene.add(new T.HemisphereLight(0xffffff, 0x14452b, 0.95));
  var sun = new T.DirectionalLight(0xffffff, 0.9); sun.position.set(5, 8, 6); scene.add(sun);
  var plant = new T.Group(); plant.rotation.x = 0.12; scene.add(plant);

  function mat(c, o) { return new T.MeshStandardMaterial(Object.assign({ color: c, roughness: 0.65, side: T.DoubleSide }, o || {})); }
  function V(x, y, z) { return new T.Vector3(x, y, z); }
  var leafMat = mat(0x7ccf80), stemMat = mat(0xa8d86e), rootMat = mat(0xe2cfae), grainMat = mat(0xe8c45a);

  function ribbon(curve, width, n) {
    var pos = [], idx = [], pts = curve.getPoints(n), i;
    for (i = 0; i <= n; i++) {
      var t = i / n, w = Math.max(0.03, width * Math.sin(Math.PI * Math.pow(t, 0.6))) / 2, p = pts[i];
      pos.push(p.x, p.y, p.z - w, p.x, p.y, p.z + w);
    }
    for (i = 0; i < n; i++) { var a = 2 * i; idx.push(a, a + 1, a + 2, a + 1, a + 3, a + 2); }
    var g = new T.BufferGeometry();
    g.setAttribute('position', new T.Float32BufferAttribute(pos, 3)); g.setIndex(idx); g.computeVertexNormals();
    return new T.Mesh(g, leafMat);
  }
  function leaf(len, rotY, tilt) {
    var g = new T.Group(), k = len / 3.6;
    var c = new T.CatmullRomCurve3([V(0, 0, 0), V(0.7 * k, 0.9, 0), V(1.7 * k, 1.4, 0), V(2.7 * k, 1.15, 0), V(3.6 * k, 0.3 + tilt, 0)]);
    g.add(ribbon(c, 0.34, 24)); g.rotation.y = rotY; return g;
  }

  // Roots
  var roots = new T.Group(), i;
  for (i = 0; i < 16; i++) {
    var a = i * 2.4, r = 0.55 + 0.45 * Math.abs(Math.sin(i * 1.7)), d = 1.6 + 0.5 * Math.abs(Math.cos(i * 2.3));
    var rc = new T.CatmullRomCurve3([V(0, 0, 0), V(Math.cos(a) * 0.2, -0.5, Math.sin(a) * 0.2), V(Math.cos(a) * r * 0.6, -1.1, Math.sin(a) * r * 0.6), V(Math.cos(a) * r, -d, Math.sin(a) * r)]);
    roots.add(new T.Mesh(new T.TubeGeometry(rc, 12, 0.025, 5), rootMat));
  }
  // Culm with nodes
  var culm = new T.Group();
  for (i = 0; i < 3; i++) {
    var seg = new T.Mesh(new T.CylinderGeometry(0.075, 0.09, 1.3, 12), stemMat); seg.position.y = 0.65 + 1.3 * i; culm.add(seg);
    var node = new T.Mesh(new T.TorusGeometry(0.1, 0.032, 8, 16), mat(0x7fae4a)); node.rotation.x = Math.PI / 2; node.position.y = 1.3 * (i + 1); culm.add(node);
  }
  // Panicle
  var pan = new T.Group(), rach = new T.CatmullRomCurve3([V(0, 0, 0), V(0, 0.7, 0), V(0.35, 1.3, 0), V(0.9, 1.5, 0), V(1.4, 1.2, 0), V(1.65, 0.55, 0)]);
  pan.add(new T.Mesh(new T.TubeGeometry(rach, 30, 0.03, 6), stemMat));
  var gg = new T.SphereGeometry(0.075, 10, 8);
  for (i = 0; i < 28; i++) {
    var t = 0.3 + 0.7 * (i / 27), p = rach.getPoint(t), th = i * 2.1, m = new T.Mesh(gg, grainMat);
    m.scale.set(1, 2.4, 0.85); m.position.set(p.x + Math.cos(th) * 0.17, p.y - 0.12 + Math.sin(i * 1.3) * 0.05, p.z + Math.sin(th) * 0.17);
    m.rotation.z = Math.cos(th) * 0.35; pan.add(m);
  }
  // Single grain: husk halves + kernel
  var grain = new T.Group(), inner = new T.Group(); grain.add(inner); inner.scale.set(0.9, 2.0, 0.9);
  var hullMat = mat(0xe8c45a, { transparent: true, opacity: 0.6 });
  var hf = new T.Mesh(new T.SphereGeometry(0.5, 24, 16, 0, Math.PI), hullMat), hb = new T.Mesh(new T.SphereGeometry(0.5, 24, 16, 0, Math.PI), hullMat);
  hb.rotation.y = Math.PI; inner.add(hf, hb, new T.Mesh(new T.SphereGeometry(0.36, 20, 14), mat(0xf6ecd2)));

  var parts = [
    { g: roots, a: V(0, 0, 0), x: V(0, -3.0, 0), lp: V(1.2, -1.1, 0), text: 'Roots' },
    { g: culm, a: V(0, 0, 0), x: V(0, 0, 0), lp: V(0.3, 0.35, 0), text: 'Culm and nodes' },
    { g: leaf(3.4, 0, 0), a: V(0, 1.3, 0), x: V(2.3, 0.9, 0), lp: V(1.9, 1.6, 0), text: 'Leaf blade' },
    { g: leaf(3.0, Math.PI, 0.1), a: V(0, 2.6, 0), x: V(-2.3, 2.2, 0), lp: V(-1.7, 1.6, 0), text: 'Leaf sheath and blade' },
    { g: leaf(2.6, -Math.PI / 3, 0.2), a: V(0, 3.9, 0), x: V(1.4, 3.6, 1.4), lp: V(1.1, 1.5, 0.9), text: 'Flag leaf' },
    { g: pan, a: V(0, 3.9, 0), x: V(-0.4, 6.1, 0), lp: V(0.9, 1.9, 0), text: 'Panicle (grain head)' },
    { g: grain, a: V(3.3, 6.2, 0), x: V(3.3, 6.2, 0), lp: V(0, -1.2, 0), text: 'Grain: husk and kernel', grain: true }
  ];
  var axis = new T.Line(new T.BufferGeometry().setFromPoints([V(0, -4.2, 0), V(0, 8.6, 0)]), new T.LineDashedMaterial({ color: 0xc8f0d0, dashSize: 0.15, gapSize: 0.12, transparent: true, opacity: 0.35 }));
  axis.computeLineDistances(); plant.add(axis);

  parts.forEach(function (p) {
    plant.add(p.g);
    p.el = document.createElement('span'); p.el.className = 'scene-label'; p.el.textContent = p.text; box.appendChild(p.el);
    if (!p.grain) {
      p.line = new T.Line(new T.BufferGeometry().setFromPoints([p.a, p.x]), new T.LineDashedMaterial({ color: 0xc8f0d0, dashSize: 0.1, gapSize: 0.1, transparent: true, opacity: 0.55 }));
      plant.add(p.line);
    }
  });

  var e = reduce ? 1 : 0, target = 1, dragging = false, lx = 0, ly = 0, btn = document.getElementById('explodeBtn');
  function update() {
    var s = e * e * (3 - 2 * e);
    parts.forEach(function (p) {
      p.g.position.lerpVectors(p.a, p.x, s);
      if (p.grain) { p.g.scale.setScalar(Math.max(0.001, s)); hf.position.z = 0.55 * s; hb.position.z = -0.55 * s; }
      else { var arr = p.line.geometry.attributes.position; arr.setXYZ(0, p.a.x, p.a.y, p.a.z); arr.setXYZ(1, p.g.position.x, p.g.position.y, p.g.position.z); arr.needsUpdate = true; p.line.computeLineDistances(); p.line.material.opacity = 0.55 * s; }
    });
  }
  function labels(w, h) {
    parts.forEach(function (p) {
      var v = p.g.position.clone().add(p.lp); plant.localToWorld(v); v.project(cam);
      p.el.style.transform = 'translate(' + ((v.x * 0.5 + 0.5) * w + 6) + 'px,' + ((-v.y * 0.5 + 0.5) * h - 10) + 'px)';
      p.el.style.opacity = p.grain ? e : 1;
    });
  }
  function resize() {
    var w = box.clientWidth, h = box.clientHeight; if (!w || !h) return;
    renderer.setSize(w, h, false); cam.aspect = w / h; cam.updateProjectionMatrix();
    var dist = Math.max(19, 5.2 / (Math.tan(19 * Math.PI / 180) * cam.aspect));
    cam.position.set(0, 2.4, dist); cam.lookAt(0, 2.2, 0);
  }
  if (window.ResizeObserver) new ResizeObserver(resize).observe(box); else window.addEventListener('resize', resize);
  resize();

  var cv = renderer.domElement;
  cv.addEventListener('pointerdown', function (ev) { dragging = true; lx = ev.clientX; ly = ev.clientY; cv.setPointerCapture(ev.pointerId); });
  cv.addEventListener('pointermove', function (ev) {
    if (!dragging) return;
    plant.rotation.y += (ev.clientX - lx) * 0.008; plant.rotation.x = Math.max(-0.4, Math.min(0.6, plant.rotation.x + (ev.clientY - ly) * 0.004)); lx = ev.clientX; ly = ev.clientY;
  });
  ['pointerup', 'pointercancel'].forEach(function (n) { cv.addEventListener(n, function () { dragging = false; }); });
  if (btn) btn.addEventListener('click', function () { target = target ? 0 : 1; btn.textContent = target ? 'Assemble' : 'Explode'; });
  if (!reduce) setTimeout(function () { e = 0.001; }, 0);

  (function loop() {
    requestAnimationFrame(loop);
    e += (target - e) * (reduce ? 1 : 0.035); if (Math.abs(target - e) < 0.0005) e = target;
    if (!dragging && !reduce) plant.rotation.y += 0.0025;
    update(); renderer.render(scene, cam); labels(box.clientWidth, box.clientHeight);
  })();
})();
