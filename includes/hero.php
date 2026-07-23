<?php
/**
 * Reusable inner-page Hero banner (page_heroes table). Every inner page
 * (About, Courses, Testimonials, Alumni, Blog, Notes, Contact) renders the
 * same markup/CSS via renderPageHero(); only the text/image differ per
 * page. The Home page's own <section class="hero"> does not use this
 * component.
 */

function getPageHero(string $slug): array
{
    static $cache = [];
    if (!array_key_exists($slug, $cache)) {
        $stmt = getDb()->prepare('SELECT * FROM page_heroes WHERE page_slug = ?');
        $stmt->execute([$slug]);
        $row = $stmt->fetch();
        $cache[$slug] = $row ?: [
            'page_slug' => $slug,
            'kicker' => '',
            'title' => '',
            'title_highlight' => '',
            'subtitle' => '',
            'breadcrumb' => '',
            'description' => '',
            'show_description' => 0,
            'background_image' => '',
        ];
    }
    return $cache[$slug];
}

/**
 * $overrides lets a page merge in a value only it can compute at request
 * time (e.g. testimonials.php's Google-rating subtitle) without needing a
 * template-placeholder system in the DB column.
 */
function renderPageHero(string $slug, array $overrides = []): void
{
    $hero = array_merge(getPageHero($slug), $overrides);

    $bgStyle = '';
    if (!empty($hero['background_image'])) {
        $bgStyle = ' style="background-image:linear-gradient(140deg,rgba(38,52,111,.88),rgba(15,21,51,.92)),url(\'' . e($hero['background_image']) . '\')"';
    }
    ?>
<div class="phero"<?= $bgStyle ?>>
  <div class="wrap reveal">
    <?php if (!empty($hero['breadcrumb'])): ?><p class="phero-crumb"><?= e($hero['breadcrumb']) ?></p><?php endif; ?>
    <?php if (!empty($hero['kicker'])): ?><div class="kick"><?= e($hero['kicker']) ?></div><?php endif; ?>
    <?php if (!empty($hero['title'])): ?>
      <h1><?= e($hero['title']) ?><?php if (!empty($hero['title_highlight'])): ?> <span class="hl"><?= e($hero['title_highlight']) ?></span><?php endif; ?></h1>
    <?php endif; ?>
    <?php if (!empty($hero['subtitle'])): ?><p class="sub"><?= e($hero['subtitle']) ?></p><?php endif; ?>
    <?php if (!empty($hero['show_description']) && !empty($hero['description'])): ?><p class="phero-desc"><?= e($hero['description']) ?></p><?php endif; ?>
  </div>
</div>
    <?php
}
