// Σχεδίαση QR στον browser (με το qrcode.js) και κατέβασμα σε PNG/SVG
(function () {
  function svgFor(text, color) {
    var qr = qrcode(0, 'M');
    qr.addData(unescape(encodeURIComponent(text)));
    qr.make();
    var n = qr.getModuleCount(), m = 2, size = n + m * 2, d = '';
    for (var r = 0; r < n; r++) for (var c = 0; c < n; c++) if (qr.isDark(r, c)) d += 'M' + (c + m) + ' ' + (r + m) + 'h1v1h-1z';
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' + size + ' ' + size + '" shape-rendering="crispEdges">'
      + '<rect width="100%" height="100%" fill="#fff"/><path d="' + d + '" fill="' + (color || '#0c101e') + '"/></svg>';
  }
  function render() {
    document.querySelectorAll('[data-qr]').forEach(function (el) {
      el.innerHTML = svgFor(el.getAttribute('data-qr'), el.getAttribute('data-qr-color'));
    });
  }
  function download(name, blob) {
    var a = document.createElement('a');
    a.href = URL.createObjectURL(blob); a.download = name;
    document.body.appendChild(a); a.click(); a.remove();
    setTimeout(function () { URL.revokeObjectURL(a.href); }, 2000);
  }
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-qr-download]');
    if (!btn) return;
    var box = document.querySelector(btn.getAttribute('data-qr-target'));
    var svg = box.innerHTML, name = btn.getAttribute('data-qr-name') || 'qr';
    if (btn.getAttribute('data-qr-download') === 'svg') {
      download(name + '.svg', new Blob([svg], { type: 'image/svg+xml' }));
      return;
    }
    var img = new Image(), px = 1200;
    img.onload = function () {
      var cv = document.createElement('canvas'); cv.width = cv.height = px;
      var ctx = cv.getContext('2d'); ctx.imageSmoothingEnabled = false; ctx.drawImage(img, 0, 0, px, px);
      cv.toBlob(function (b) { download(name + '.png', b); }, 'image/png');
    };
    img.src = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svg);
  });
  // Εμφάνιση πεδίων ανάλογα με τον τύπο QR
  function syncType() {
    var sel = document.getElementById('qrtype');
    if (!sel) return;
    document.querySelectorAll('[data-show]').forEach(function (el) {
      el.style.display = el.getAttribute('data-show') === sel.value ? '' : 'none';
    });
  }
  document.addEventListener('change', function (e) { if (e.target.id === 'qrtype') syncType(); });
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', function () { render(); syncType(); });
  else { render(); syncType(); }
})();
