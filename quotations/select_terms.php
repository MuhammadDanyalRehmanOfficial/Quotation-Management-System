<?php
require __DIR__ . '/../config/db.php';
if (!isset($_SESSION['user'])) {
    header('Location: /index.php');
    exit;
}
$pageTitle = "Select Terms";
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

$qid = (int)($_GET['id'] ?? 0);
if ($qid <= 0) {
    header('Location: /dashboard.php');
    exit;
}

$msg = '';
// Save selected terms as snapshots (modifiable for this quotation only)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Clear old snapshots
    $mysqli->query("DELETE FROM quotation_terms WHERE quotation_id=$qid");

    // Save selected defaults
    if (!empty($_POST['term_ids']) && is_array($_POST['term_ids'])) {
        $sel = $mysqli->prepare("SELECT title,body FROM terms WHERE id=?");
        $ins = $mysqli->prepare("INSERT INTO quotation_terms(quotation_id,title_snapshot,body_snapshot) VALUES(?,?,?)");
        foreach ($_POST['term_ids'] as $tid) {
            $tid = (int)$tid;
            $sel->bind_param('i', $tid);
            $sel->execute();
            if ($t = $sel->get_result()->fetch_assoc()) {
                $ins->bind_param('iss', $qid, $t['title'], $t['body']);
                $ins->execute();
            }
        }
    }

    // Also apply any custom overrides (per-quotation)
    if (!empty($_POST['custom_titles']) && !empty($_POST['custom_bodies'])) {
        $ins = $mysqli->prepare("INSERT INTO quotation_terms(quotation_id,title_snapshot,body_snapshot) VALUES(?,?,?)");
        foreach ($_POST['custom_titles'] as $i => $ctitle) {
            $ctitle = trim($ctitle);
            $cbody  = trim($_POST['custom_bodies'][$i] ?? '');
            if ($ctitle && $cbody) {
                $ins->bind_param('iss', $qid, $ctitle, $cbody);
                $ins->execute();
            }
        }
    }

    header("Location: finalize.php?id=" . $qid);
    exit;
}

$defaults = $mysqli->query("SELECT * FROM terms ORDER BY id DESC");
$existing = $mysqli->query("SELECT * FROM quotation_terms WHERE quotation_id=$qid")->fetch_all(MYSQLI_ASSOC);
?>
<div class="container mt-5">
    <!-- Page Title -->
    <h3 class="fw-bold mb-4 text-center">📑 Step 4: Select Terms & Conditions</h3>

    <form method="post" class="row g-4">
        <!-- Default Terms -->
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-light fw-bold">✔️ Default Terms</div>
                <div class="card-body">
                    <?php if ($defaults->num_rows > 0): ?>
                        <?php while ($t = $defaults->fetch_assoc()): ?>
                            <div class="form-check mb-3 p-4 border rounded">
                                <input class="form-check-input" type="checkbox"
                                    name="term_ids[]" value="<?= (int)$t['id'] ?>"
                                    id="t<?= (int)$t['id'] ?>">
                                <label class="form-check-label" for="t<?= (int)$t['id'] ?>">
                                    <strong><?= esc($t['title']) ?></strong>
                                    <div class="small text-muted mt-1"><?= nl2br(esc($t['body'])) ?></div>
                                </label>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-muted fst-italic">No default terms found. Add some first.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Custom Terms -->
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-light fw-bold">✏️ Custom / Modified Terms</div>
                <div class="card-body" id="customTerms">
                    <div class="row g-3 customTermRow mb-3">
                        <div class="col-md-4">
                            <input class="form-control" name="custom_titles[]" placeholder="Title">
                        </div>
                        <div class="col-md-8">
                            <textarea class="form-control" name="custom_bodies[]" rows="2" placeholder="Body"></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addCustom()">➕ Add another</button>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="d-flex justify-content-between mt-3">
            <a class="btn btn-outline-secondary" href="add_items.php?id=<?= (int)$qid ?>">⬅ Back</a>
            <button class="btn btn-success">Next: Finalize ➡</button>
        </div>
    </form>
</div>

<script>
    function addCustom() {
        const row = document.querySelector('.customTermRow').cloneNode(true);
        row.querySelectorAll('input,textarea').forEach(el => el.value = '');
        document.getElementById('customTerms').appendChild(row);
    }
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>