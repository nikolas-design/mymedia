<?= ai_tabs('posts') ?>
<?php if ($profileEmpty && $canEdit): ?>
  <div class="flash info">Πες μας πρώτα λίγα λόγια για την επιχείρησή σου, για να γράφει το AI στο δικό σου ύφος. <a href="<?= e(module_url('ai-content', 'profile')) ?>">Το ύφος σου</a></div>
<?php endif; ?>
<div style="display:flex;justify-content:space-between;gap:8px;flex-wrap:wrap;margin-bottom:12px">
  <div class="btns">
    <?php foreach (['' => 'Επερχόμενα', 'draft' => 'Προτάσεις', 'approved' => 'Εγκεκριμένα', 'posted' => 'Δημοσιευμένα'] as $k => $v): ?>
      <a class="btn sm<?= $status === $k ? ' dark' : '' ?>" href="<?= e(module_url('ai-content', $k ? '?status=' . $k : '')) ?>"><?= e($v) ?></a>
    <?php endforeach; ?>
  </div>
  <div class="btns"><a class="btn sm" href="<?= e(module_url('ai-content', 'posts/new')) ?>"><?= icon('plus', 14) ?> Δικό μου post</a><a class="btn sm dark" href="<?= e(module_url('ai-content', 'generate')) ?>">✨ Νέες προτάσεις</a></div>
</div>
<div class="cols" style="grid-template-columns:repeat(auto-fill,minmax(300px,1fr))">
  <?php foreach ($posts as $p): ?>
    <a class="card" href="<?= e(module_url('ai-content', 'posts/' . $p['id'])) ?>" style="color:inherit;text-decoration:none;display:block">
      <div style="display:flex;justify-content:space-between;align-items:center;gap:8px;margin-bottom:8px">
        <span class="small"><b><?= $p['for_date'] ? e(['', 'Δευ', 'Τρί', 'Τετ', 'Πέμ', 'Παρ', 'Σάβ', 'Κυρ'][(int) date('N', strtotime($p['for_date']))] . ' ' . date_gr($p['for_date'], false)) : 'Χωρίς ημερομηνία' ?></b> · <?= e(AI_PLATFORMS[$p['platform']] ?? $p['platform']) ?></span>
        <?= pill(...AI_STATUS[$p['status']]) ?>
      </div>
      <?php if ($p['title']): ?><b style="display:block;margin-bottom:4px"><?= e($p['title']) ?></b><?php endif; ?>
      <p class="small" style="margin:0;color:var(--soft);display:-webkit-box;-webkit-line-clamp:4;-webkit-box-orient:vertical;overflow:hidden"><?= e($p['caption']) ?></p>
      <?php if ($p['image_idea']): ?><p class="small muted" style="margin:8px 0 0">📷 <?= e($p['image_idea']) ?></p><?php endif; ?>
    </a>
  <?php endforeach; ?>
</div>
<?php if (!$posts): ?><div class="card empty">Δεν υπάρχουν posts εδώ. Πάτα «✨ Νέες προτάσεις» για να φτιάξει το AI το πλάνο της εβδομάδας.</div><?php endif; ?>
