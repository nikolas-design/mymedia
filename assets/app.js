// Επιβεβαίωση πριν από ενέργειες: <form data-confirm="Σίγουρα;">
document.addEventListener('submit', function (e) {
  var msg = e.target.getAttribute('data-confirm');
  if (msg && !window.confirm(msg)) e.preventDefault();
});

// Αντιγραφή συνδέσμου: <button data-copy="#id">
document.addEventListener('click', function (e) {
  var btn = e.target.closest('[data-copy]');
  if (!btn) return;
  var input = document.querySelector(btn.getAttribute('data-copy'));
  if (!input) return;
  input.select();
  var done = function () { btn.textContent = 'Αντιγράφηκε'; };
  if (navigator.clipboard) navigator.clipboard.writeText(input.value).then(done, function () { document.execCommand('copy'); done(); });
  else { document.execCommand('copy'); done(); }
});

// Εκτύπωση: <button data-print>
document.addEventListener('click', function (e) {
  if (e.target.closest('[data-print]')) window.print();
});
