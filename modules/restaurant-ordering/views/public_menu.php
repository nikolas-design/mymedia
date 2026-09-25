<style>.pub{text-align:left;padding-bottom:120px}.pub h1,.pub .lead{text-align:center}.ro-it{display:flex;gap:12px;padding:12px 0;border-bottom:1px solid var(--line);align-items:center}.ro-it img{width:64px;height:64px;object-fit:cover;border-radius:10px}.ro-it .t{flex:1;min-width:0}.ro-it b{display:block}.ro-it small{color:var(--muted)}.add{border:0;background:var(--brand);color:#fff;border-radius:10px;width:40px;height:40px;font-size:22px;cursor:pointer}.cartbar{position:fixed;left:0;right:0;bottom:0;background:#fff;border-top:1px solid var(--line);padding:10px 14px calc(10px + env(safe-area-inset-bottom));box-shadow:0 -8px 24px rgba(0,0,0,.06)}.cartbar .cta{margin:0}.qty{display:inline-flex;align-items:center;gap:6px}.qty button{width:30px;height:30px;border-radius:8px;border:1px solid var(--line);background:#fff;cursor:pointer}.seg{display:flex;gap:6px}.seg label{flex:1;text-align:center;border:1px solid var(--line);border-radius:10px;padding:10px;margin:0;cursor:pointer;font-weight:600}.seg label:has(input:checked){background:var(--brand);color:#fff;border-color:var(--brand)}.seg input{display:none}</style>
<h1><?= e($st['title']) ?></h1>
<p class="lead"><?= e(implode(' · ', array_filter([
    $st['hours'] ?? '',
    $st['delivery'] && $st['delivery_fee_cents'] ? 'αποστολή ' . money($st['delivery_fee_cents']) : '',
    $st['min_order_cents'] ? 'ελάχιστη ' . money($st['min_order_cents']) : '',
]))) ?></p>
<?php if (!empty($error)): ?><div class="err"><?= e($error) ?></div><?php endif; ?>
<?php if (!$st['accepting']): ?><div class="err">Αυτή τη στιγμή δεν δεχόμαστε online παραγγελίες.<?= $st['phone'] ? ' Τηλ. ' . e($st['phone']) : '' ?></div><?php endif; ?>
<?php if ($st['note']): ?><div class="ok"><?= e($st['note']) ?></div><?php endif; ?>

<?php foreach ($categories as $c): if (empty($items[(int) $c['id']])) continue; ?>
  <h2 style="font-size:19px;margin:22px 0 4px"><?= e($c['name']) ?></h2>
  <?php foreach ($items[(int) $c['id']] as $i): ?>
    <div class="ro-it" style="<?= $i['available'] ? '' : 'opacity:.45' ?>">
      <?php if ($i['photo']): ?><img src="<?= e(upload_url($i['photo'])) ?>" alt="" loading="lazy"><?php endif; ?>
      <div class="t"><b><?= e($i['name']) ?></b><?php if ($i['description']): ?><small><?= e($i['description']) ?></small><br><?php endif; ?><small style="color:var(--brand);font-weight:700"><?= money($i['price_cents']) ?></small></div>
      <?php if ($i['available'] && $st['accepting']): ?><button class="add" type="button" data-add='<?= e(json_encode(['id' => (int) $i['id'], 'name' => $i['name'], 'price' => (int) $i['price_cents']], JSON_UNESCAPED_UNICODE)) ?>'>+</button><?php elseif (!$i['available']): ?><small>Εξαντλήθηκε</small><?php endif; ?>
    </div>
  <?php endforeach; ?>
<?php endforeach; ?>

<form method="post" id="checkout" class="pcard" style="display:none;margin-top:20px">
  <?= csrf_field() ?><input type="hidden" name="cart" id="cartjson">
  <b>Η παραγγελία σας</b>
  <div id="cartlines" class="list"></div>
  <div class="seg mt">
    <?php if ($st['delivery']): ?><label><input type="radio" name="kind" value="delivery" checked> Delivery</label><?php endif; ?>
    <?php if ($st['takeaway']): ?><label><input type="radio" name="kind" value="takeaway" <?= $st['delivery'] ? '' : 'checked' ?>> Take away</label><?php endif; ?>
  </div>
  <label>Ονοματεπώνυμο</label><input name="name" required autocomplete="name">
  <label>Κινητό</label><input name="phone" type="tel" required autocomplete="tel">
  <div id="addr"><label>Διεύθυνση</label><input name="address" autocomplete="street-address" placeholder="Οδός, αριθμός, περιοχή"><label>Όροφος / κουδούνι</label><input name="floor_bell"></div>
  <label>Σχόλια</label><input name="notes" placeholder="π.χ. χωρίς κρεμμύδι">
  <label>Πληρωμή</label><div class="seg"><label><input type="radio" name="payment" value="cash" checked> Μετρητά</label><label><input type="radio" name="payment" value="card"> Κάρτα (POS)</label></div>
  <button class="cta">Αποστολή παραγγελίας</button>
</form>

<div class="cartbar" id="cartbar" style="display:none"><button class="cta" type="button" id="gocheck">Καλάθι · <span id="carttotal"></span></button></div>
<script>
(function () {
  var key = 'ro_cart_<?= e($st['code']) ?>', fee = <?= (int) $st['delivery_fee_cents'] ?>, cart = [];
  try { cart = JSON.parse(localStorage.getItem(key) || '[]'); } catch (e) {}
  function m(c) { return '€' + (c / 100).toLocaleString('el-GR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
  function save() { try { localStorage.setItem(key, JSON.stringify(cart)); } catch (e) {} render(); }
  function sub() { return cart.reduce(function (s, x) { return s + x.qty * x.price; }, 0); }
  function render() {
    var bar = document.getElementById('cartbar'), co = document.getElementById('checkout');
    bar.style.display = cart.length ? '' : 'none'; if (!cart.length) co.style.display = 'none';
    document.getElementById('carttotal').textContent = m(sub()) + ' (' + cart.reduce(function (s, x) { return s + x.qty; }, 0) + ')';
    var box = document.getElementById('cartlines'); box.innerHTML = '';
    cart.forEach(function (x, i) {
      var d = document.createElement('div'); d.className = 'it';
      d.innerHTML = '<span></span><span class="qty"><button type="button" data-d="-1">−</button><b></b><button type="button" data-d="1">+</button></span>';
      d.firstChild.textContent = x.name + ' · ' + m(x.price * x.qty); d.querySelector('b').textContent = x.qty;
      d.querySelectorAll('button').forEach(function (b) { b.onclick = function () { x.qty += parseInt(b.dataset.d, 10); if (x.qty < 1) cart.splice(i, 1); save(); }; });
      box.appendChild(d);
    });
    var del = co.querySelector('input[name=kind][value=delivery]'), isDel = del && del.checked;
    var t = document.createElement('div'); t.className = 'it'; t.innerHTML = '<b>Σύνολο</b><b></b>';
    t.lastChild.textContent = m(sub() + (isDel ? fee : 0)) + (isDel && fee ? ' (με αποστολή)' : ''); box.appendChild(t);
    document.getElementById('addr').style.display = isDel ? '' : 'none';
    document.getElementById('cartjson').value = JSON.stringify(cart.map(function (x) { return { id: x.id, qty: x.qty }; }));
  }
  document.addEventListener('click', function (e) {
    var b = e.target.closest('[data-add]'); if (!b) return;
    var it = JSON.parse(b.dataset.add), ex = cart.find(function (x) { return x.id === it.id; });
    if (ex) ex.qty++; else cart.push({ id: it.id, name: it.name, price: it.price, qty: 1 });
    save(); b.textContent = '✓'; setTimeout(function () { b.textContent = '+'; }, 600);
  });
  document.getElementById('gocheck').onclick = function () { var co = document.getElementById('checkout'); co.style.display = ''; co.scrollIntoView({ behavior: 'smooth' }); };
  document.getElementById('checkout').addEventListener('change', render);
  document.getElementById('checkout').addEventListener('submit', function () { try { localStorage.removeItem(key); } catch (e) {} });
  render();
})();
</script>
