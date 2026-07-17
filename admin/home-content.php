<?php
require_once __DIR__ . '/partials/header.php';

$db = getDb();

// Plain-text content_blocks (page_slug = 'home') edited on this screen.
$textBlocks = [
    'track_record_heading' => 'Track Record - Heading',
    'track_record_description' => 'Track Record - Description',
    'founders_heading' => "Founders' Vision - Section Label",
    'why_heading' => 'Why EnglishKeys - Heading',
];
// Image-only content_blocks (page_slug = 'home').
$imageBlocks = [
    'track_record_bg' => ['label' => 'Track Record - Background Image (optional, low-opacity overlay)', 'subdir' => 'home'],
    'fc_popup_bg' => ['label' => 'Popup - Background Image (optional, shows behind the card content)', 'subdir' => 'home'],
];
// Must match the names defined in includes/icons.php icon().
$iconNames = ['cap', 'target', 'people', 'chat', 'mail', 'book', 'document', 'calendar', 'card', 'ticket', 'star', 'check', 'plus', 'person', 'trophy', 'medal', 'award'];

$teachers = $db->query('SELECT id, name, role_title FROM teachers ORDER BY sort_order, id')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $sectionForm = $_POST['section_form'] ?? 'content';

    if ($sectionForm === 'why_card') {
        $action = $_POST['action'] ?? '';

        if ($action === 'delete') {
            $db->prepare('DELETE FROM home_why_cards WHERE id = ?')->execute([(int)$_POST['id']]);
            redirectWithMessage('home-content.php#why-cards-all', 'Card deleted.');
        }

        if ($action === 'save') {
            $id = (int)($_POST['id'] ?? 0);
            $icon = in_array($_POST['icon'] ?? '', $iconNames, true) ? $_POST['icon'] : 'cap';
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $sortOrder = (int)($_POST['sort_order'] ?? 0);
            $isActive = isset($_POST['is_active']) ? 1 : 0;

            if ($title === '') {
                redirectWithMessage('home-content.php#why-cards-add', 'Title is required.', 'error');
            }

            if ($id > 0) {
                $db->prepare('UPDATE home_why_cards SET icon=?, title=?, description=?, sort_order=?, is_active=? WHERE id=?')
                   ->execute([$icon, $title, $description, $sortOrder, $isActive, $id]);
                redirectWithMessage('home-content.php#why-cards-all', 'Card updated.');
            } else {
                $db->prepare('INSERT INTO home_why_cards (icon, title, description, sort_order, is_active) VALUES (?,?,?,?,?)')
                   ->execute([$icon, $title, $description, $sortOrder, $isActive]);
                redirectWithMessage('home-content.php#why-cards-all', 'Card added.');
            }
        }
    } else {
        $stmt = $db->prepare('UPDATE site_settings SET setting_value = ? WHERE setting_key = ?');
        $stmt->execute([trim($_POST['hero_title'] ?? ''), 'hero_title']);
        $stmt->execute([trim($_POST['hero_subtitle'] ?? ''), 'hero_subtitle']);
        $stmt->execute([trim($_POST['hero_micro'] ?? ''), 'hero_micro']);
        $stmt->execute([trim($_POST['hero_cta1_label'] ?? ''), 'hero_cta1_label']);
        $stmt->execute([trim($_POST['hero_cta1_link'] ?? ''), 'hero_cta1_link']);
        $stmt->execute([trim($_POST['hero_cta2_label'] ?? ''), 'hero_cta2_label']);
        $stmt->execute([trim($_POST['hero_cta2_link'] ?? ''), 'hero_cta2_link']);
        $stmt->execute([trim($_POST['fc_popup_card_width'] ?? ''), 'fc_popup_card_width']);
        $stmt->execute([trim($_POST['fc_popup_btn1_label'] ?? ''), 'fc_popup_btn1_label']);
        $stmt->execute([trim($_POST['fc_popup_btn1_link'] ?? ''), 'fc_popup_btn1_link']);
        $stmt->execute([trim($_POST['fc_popup_btn2_label'] ?? ''), 'fc_popup_btn2_label']);
        $stmt->execute([trim($_POST['fc_popup_btn2_link'] ?? ''), 'fc_popup_btn2_link']);

        $teacherIds = array_column($teachers, 'id');
        $foundersTeacherId = (int)($_POST['founders_vision_teacher_id'] ?? 0);
        if (in_array($foundersTeacherId, $teacherIds, true)) {
            $stmt->execute([$foundersTeacherId, 'founders_vision_teacher_id']);
        }

        $blockStmt = $db->prepare(
            'INSERT INTO content_blocks (page_slug, block_key, content) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE content = VALUES(content)'
        );
        foreach ($textBlocks as $key => $label) {
            $blockStmt->execute(['home', $key, trim($_POST[$key] ?? '')]);
        }

        try {
            $heroImage = handleImageUpload('hero_image', 'logo');
            if ($heroImage) {
                $stmt->execute([$heroImage, 'hero_image']);
            }

            $imageStmt = $db->prepare(
                'INSERT INTO content_blocks (page_slug, block_key, image_path) VALUES (?, ?, ?)
                 ON DUPLICATE KEY UPDATE image_path = VALUES(image_path)'
            );
            if (isset($_POST['fc_popup_bg_remove'])) {
                $imageStmt->execute(['home', 'fc_popup_bg', null]);
            }
            foreach ($imageBlocks as $key => $meta) {
                $uploaded = handleImageUpload($key, $meta['subdir']);
                if ($uploaded) {
                    $imageStmt->execute(['home', $key, $uploaded]);
                }
            }
            redirectWithMessage('home-content.php', 'Homepage content updated.');
        } catch (RuntimeException $e) {
            redirectWithMessage('home-content.php', $e->getMessage(), 'error');
        }
    }
}

$stmt = $db->query('SELECT setting_key, setting_value FROM site_settings');
$settings = [];
foreach ($stmt->fetchAll() as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

$blockValues = [];
foreach ($textBlocks as $key => $label) {
    $blockValues[$key] = getContentBlock('home', $key)['content'] ?? '';
}
$imageValues = [];
foreach ($imageBlocks as $key => $meta) {
    $imageValues[$key] = getContentBlock('home', $key)['image_path'] ?? '';
}

$editingCard = null;
if (isset($_GET['edit_card'])) {
    $cardStmt = $db->prepare('SELECT * FROM home_why_cards WHERE id = ?');
    $cardStmt->execute([(int)$_GET['edit_card']]);
    $editingCard = $cardStmt->fetch();
}
$whyCards = $db->query('SELECT * FROM home_why_cards ORDER BY sort_order, id')->fetchAll();
?>
<div class="admin-tabs-page">
<h1>Homepage Content</h1>
<p class="admin-page-intro">Everything shown on the public Home page, grouped by section. Pick a tab to edit just that part &mdash; changes in the top form save together, Why Us Cards save separately below.</p>

<nav class="admin-tabbar" role="tablist" aria-label="Homepage sections">
  <button type="button" class="admin-tab active" data-tab-group="main" data-tab-target="hero" role="tab" aria-selected="true"><?= icon('star', 'tab-icon') ?> Hero</button>
  <button type="button" class="admin-tab" data-tab-group="main" data-tab-target="courses" role="tab" aria-selected="false"><?= icon('book', 'tab-icon') ?> Courses</button>
  <button type="button" class="admin-tab" data-tab-group="main" data-tab-target="track-record" role="tab" aria-selected="false"><?= icon('trophy', 'tab-icon') ?> Track Record</button>
  <button type="button" class="admin-tab" data-tab-group="main" data-tab-target="founders-vision" role="tab" aria-selected="false"><?= icon('person', 'tab-icon') ?> Founders&rsquo; Vision</button>
  <button type="button" class="admin-tab" data-tab-group="main" data-tab-target="why-englishkeys" role="tab" aria-selected="false"><?= icon('award', 'tab-icon') ?> Why EnglishKeys</button>
  <button type="button" class="admin-tab" data-tab-group="main" data-tab-target="popup" role="tab" aria-selected="false"><?= icon('ticket', 'tab-icon') ?> Featured Popup</button>
  <button type="button" class="admin-tab" data-tab-group="main" data-tab-target="why-cards" role="tab" aria-selected="false"><?= icon('card', 'tab-icon') ?> Why Us Cards <span class="tab-count"><?= count($whyCards) ?></span></button>
</nav>

<form method="post" enctype="multipart/form-data" class="admin-form">
  <?= csrfField() ?>
  <input type="hidden" name="section_form" value="content">

  <div class="admin-tabpanel" id="hero" data-tab-group="main" data-tab-id="hero">
    <h2>Hero Section</h2>
    <label>Hero Title
      <input type="text" name="hero_title" value="<?= e($settings['hero_title'] ?? '') ?>">
    </label>
    <label>Hero Subtitle
      <textarea name="hero_subtitle" rows="2"><?= e($settings['hero_subtitle'] ?? '') ?></textarea>
    </label>
    <label>Hero Micro Line (small line under the CTA buttons)
      <input type="text" name="hero_micro" value="<?= e($settings['hero_micro'] ?? '') ?>">
    </label>
    <label>Primary Button Label
      <input type="text" name="hero_cta1_label" value="<?= e($settings['hero_cta1_label'] ?? 'Explore Courses') ?>">
    </label>
    <label>Primary Button Link
      <input type="text" name="hero_cta1_link" value="<?= e($settings['hero_cta1_link'] ?? 'courses.php') ?>">
    </label>
    <label>Secondary Button Label
      <input type="text" name="hero_cta2_label" value="<?= e($settings['hero_cta2_label'] ?? 'See Our Results') ?>">
    </label>
    <label>Secondary Button Link
      <input type="text" name="hero_cta2_link" value="<?= e($settings['hero_cta2_link'] ?? '#results') ?>">
    </label>
    <label>Hero Image
      <?php if (!empty($settings['hero_image'])): ?>
        <div><img src="../<?= e($settings['hero_image']) ?>" alt="Current hero image" style="max-height:120px;"></div>
      <?php endif; ?>
      <input type="file" name="hero_image" accept=".jpg,.jpeg,.png,.webp">
    </label>
  </div>

  <div class="admin-tabpanel" id="courses" data-tab-group="main" data-tab-id="courses" hidden>
    <h2>Courses</h2>
    <div class="admin-note">
      <?= icon('document', 'note-icon') ?>
      <p>The "Our Courses" section pulls live from Core Subjects. Add, edit, delete, reorder and enable/disable them (and Featured/Programme courses used elsewhere) under <a href="courses.php">Courses &rarr;</a></p>
    </div>
  </div>

  <div class="admin-tabpanel" id="track-record" data-tab-group="main" data-tab-id="track-record" hidden>
    <h2>Proven Track Record</h2>
    <label>Heading
      <input type="text" name="track_record_heading" value="<?= e($blockValues['track_record_heading']) ?>">
    </label>
    <label>Description
      <textarea name="track_record_description" rows="2"><?= e($blockValues['track_record_description']) ?></textarea>
    </label>
    <label><?= e($imageBlocks['track_record_bg']['label']) ?>
      <?php if (!empty($imageValues['track_record_bg'])): ?>
        <div><img src="../<?= e($imageValues['track_record_bg']) ?>" style="max-height:100px;"></div>
      <?php endif; ?>
      <input type="file" name="track_record_bg" accept=".jpg,.jpeg,.png,.webp">
    </label>
  </div>

  <div class="admin-tabpanel" id="founders-vision" data-tab-group="main" data-tab-id="founders-vision" hidden>
    <h2>Founders&rsquo; Vision</h2>
    <div class="admin-note">
      <?= icon('document', 'note-icon') ?>
      <p>A quote-only section: section label, the quote, and the founder's name and title. The quote itself is edited under Page Content -&gt; About - Founders Quote (this section is hidden automatically when that quote is empty). Name and title are pulled from <a href="teachers.php">Our Team &rarr;</a>, just pick which team member is credited below.</p>
    </div>
    <label>Section Label
      <input type="text" name="founders_heading" value="<?= e($blockValues['founders_heading']) ?>">
    </label>
    <label>Founder credited
      <select name="founders_vision_teacher_id">
        <?php foreach ($teachers as $teacher): ?>
          <option value="<?= (int)$teacher['id'] ?>" <?= (int)($settings['founders_vision_teacher_id'] ?? 0) === (int)$teacher['id'] ? 'selected' : '' ?>>
            <?= e($teacher['name']) ?><?= $teacher['role_title'] ? ' - ' . e($teacher['role_title']) : '' ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>
  </div>

  <div class="admin-tabpanel" id="why-englishkeys" data-tab-group="main" data-tab-id="why-englishkeys" hidden>
    <h2>Why EnglishKeys</h2>
    <div class="admin-note">
      <?= icon('card', 'note-icon') ?>
      <p>The cards themselves are managed under the <strong>Why Us Cards</strong> tab.</p>
    </div>
    <label>Heading
      <input type="text" name="why_heading" value="<?= e($blockValues['why_heading']) ?>">
    </label>
  </div>

  <div class="admin-tabpanel" id="popup" data-tab-group="main" data-tab-id="popup" hidden>
    <h2>Featured Course Popup</h2>
    <div class="admin-note">
      <?= icon('ticket', 'note-icon') ?>
      <p>The "Enrolling Now" popup that appears a second after loading the Home page. Its title, description and details come from whichever course is marked Featured under Courses &mdash; only the card's presentation is configured here.</p>
    </div>
    <label>Card Width (px)
      <input type="number" name="fc_popup_card_width" min="300" max="700" value="<?= e($settings['fc_popup_card_width'] ?? '430') ?>">
    </label>
    <label><?= e($imageBlocks['fc_popup_bg']['label']) ?>
      <?php if (!empty($imageValues['fc_popup_bg'])): ?>
        <div><img src="../<?= e($imageValues['fc_popup_bg']) ?>" style="max-height:100px;"></div>
        <label class="checkbox-label"><input type="checkbox" name="fc_popup_bg_remove" value="1"> Remove this image</label>
      <?php endif; ?>
      <input type="file" name="fc_popup_bg" accept=".jpg,.jpeg,.png,.webp">
    </label>
    <label>Button 1 Label
      <input type="text" name="fc_popup_btn1_label" value="<?= e($settings['fc_popup_btn1_label'] ?? 'Enrol Now') ?>">
    </label>
    <label>Button 1 Link
      <input type="text" name="fc_popup_btn1_link" value="<?= e($settings['fc_popup_btn1_link'] ?? 'enroll.php') ?>">
    </label>
    <label>Button 2 Label
      <input type="text" name="fc_popup_btn2_label" value="<?= e($settings['fc_popup_btn2_label'] ?? 'See All Courses') ?>">
    </label>
    <label>Button 2 Link
      <input type="text" name="fc_popup_btn2_link" value="<?= e($settings['fc_popup_btn2_link'] ?? 'courses.php') ?>">
    </label>
  </div>

  <button type="submit">Save</button>
</form>

<div class="admin-tabpanel admin-section-card" id="why-cards" data-tab-group="main" data-tab-id="why-cards" hidden>
  <h2>Why Us Cards</h2>
  <div class="admin-note">
    <?= icon('card', 'note-icon') ?>
    <p>The 3 (or more) cards in the "Why EnglishKeys" section on the Home page. Add, edit, delete, reorder with Sort Order, and hide with Visible. The section's heading is edited under the <strong>Why EnglishKeys</strong> tab above.</p>
  </div>

  <nav class="admin-tabbar admin-tabbar-nested" role="tablist" aria-label="Why Us Cards sections">
    <button type="button" class="admin-tab" data-tab-group="why-cards" data-tab-target="why-cards-add" role="tab" aria-selected="false"><?= icon('plus', 'tab-icon') ?> <?= $editingCard ? 'Edit Card' : 'Add Card' ?></button>
    <button type="button" class="admin-tab active" data-tab-group="why-cards" data-tab-target="why-cards-all" role="tab" aria-selected="true"><?= icon('card', 'tab-icon') ?> All Cards <span class="tab-count"><?= count($whyCards) ?></span></button>
  </nav>

  <div class="admin-tabpanel-nested" id="why-cards-add" data-tab-group="why-cards" data-tab-id="why-cards-add" hidden>
    <form method="post" class="admin-form">
      <?= csrfField() ?>
      <input type="hidden" name="section_form" value="why_card">
      <input type="hidden" name="action" value="save">
      <input type="hidden" name="id" value="<?= (int)($editingCard['id'] ?? 0) ?>">

      <label>Icon
        <select name="icon">
          <?php foreach ($iconNames as $name): ?>
            <option value="<?= e($name) ?>" <?= ($editingCard['icon'] ?? 'cap') === $name ? 'selected' : '' ?>><?= e($name) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Title
        <input type="text" name="title" value="<?= e($editingCard['title'] ?? '') ?>" required>
      </label>
      <label>Description
        <textarea name="description" rows="3"><?= e($editingCard['description'] ?? '') ?></textarea>
      </label>
      <label>Sort Order
        <input type="number" name="sort_order" value="<?= (int)($editingCard['sort_order'] ?? 0) ?>">
      </label>
      <label class="checkbox-label">
        <input type="checkbox" name="is_active" <?= (!isset($editingCard) || $editingCard['is_active']) ? 'checked' : '' ?>>
        Visible on site
      </label>

      <button type="submit"><?= $editingCard ? 'Update' : 'Add Card' ?></button>
      <?php if ($editingCard): ?><a href="home-content.php#why-cards-all" class="button-secondary">Cancel</a><?php endif; ?>
    </form>
  </div>

  <div class="admin-tabpanel-nested" id="why-cards-all" data-tab-group="why-cards" data-tab-id="why-cards-all">
    <table class="admin-table">
      <thead>
        <tr><th>Icon</th><th>Title</th><th>Sort Order</th><th>Visible</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach ($whyCards as $card): ?>
          <tr>
            <td><?= e($card['icon']) ?></td>
            <td><?= e($card['title']) ?></td>
            <td><?= (int)$card['sort_order'] ?></td>
            <td><?= $card['is_active'] ? 'Yes' : 'No' ?></td>
            <td>
              <a href="home-content.php?edit_card=<?= (int)$card['id'] ?>#why-cards-add">Edit</a>
              <form method="post" class="inline-form" onsubmit="return confirm('Delete this card?');">
                <?= csrfField() ?>
                <input type="hidden" name="section_form" value="why_card">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$card['id'] ?>">
                <button type="submit" class="link-button">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
