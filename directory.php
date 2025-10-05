<?php
$page_title = 'About Daet';
$activePage = 'about';

require_once __DIR__ . '/includes/directory_loader.php';

$directoryPath = __DIR__ . '/uploads/directory.json';
$directoryError = null;
$directoryEntries = load_directory_entries($directoryPath, $directoryError);

if (!function_exists('directory_normalize_url')) {
    function directory_normalize_url(?string $url): ?string
    {
        if ($url === null) {
            return null;
        }

        $trimmed = trim($url);
        if ($trimmed === '') {
            return null;
        }

        if (!preg_match('#^https?://#i', $trimmed)) {
            $trimmed = 'https://' . ltrim($trimmed, '/');
        }

        return filter_var($trimmed, FILTER_VALIDATE_URL) ? $trimmed : null;
    }
}

if (!function_exists('directory_format_tel_href')) {
    function directory_format_tel_href(?string $number): ?string
    {
        if ($number === null) {
            return null;
        }

        $digits = preg_replace('/[^0-9+]/', '', $number);
        if ($digits === '') {
            return null;
        }

        return 'tel:' . $digits;
    }
}

$groupedDirectory = [];
foreach ($directoryEntries as $entry) {
    $office = $entry['OFFICE/DEPARTMENT'] ?? 'Unspecified Office';
    if (!isset($groupedDirectory[$office])) {
        $groupedDirectory[$office] = [];
    }
    $groupedDirectory[$office][] = $entry;
}

if (!empty($groupedDirectory)) {
    ksort($groupedDirectory, SORT_NATURAL | SORT_FLAG_CASE);
    foreach ($groupedDirectory as &$records) {
        usort($records, static function ($a, $b) {
            return strcasecmp($a['HEAD OF OFFICE'] ?? '', $b['HEAD OF OFFICE'] ?? '');
        });
    }
    unset($records);
}

include 'header.php';
?>

<!-- Reusable Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0 text-center">
            <img id="modalImage" src="" class="img-fluid rounded shadow" alt="Zoomed image">
        </div>
    </div>
</div>

<!-- HERO SECTION -->
<header class="about-header text-white text-center d-flex align-items-center justify-content-center"
    style="background: url(assets/img/aboutdaet.png); height: 40vh;">
    <div class="container px-4 px-lg-5">
        <h1 class="fw-bold display-4">MUNICIPAL DIRECTORY OF DAET</h1>
    </div>
</header>

<section class="about-daet py-5" style="background-color: #fff;">
    <div class="container px-4 px-lg-5">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="about.php">Know Daet</a></li>
                <li class="breadcrumb-item active" aria-current="page">Municipal Directory</li>
            </ol>
        </nav>

        <div class="row gx-5">
            <div class="about-left col-lg-8">
                <p class="text-muted mb-4">
                    Explore the complete municipal directory for Daet, featuring contact information and digital channels for each office.
                </p>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12 col-lg-6 ms-lg-auto">
                <label for="directorySearch" class="form-label visually-hidden">Search directory</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="search" id="directorySearch" class="form-control" placeholder="Search by office, head, or contact">
                </div>
            </div>
        </div>

        <?php if ($directoryError !== null) : ?>
            <div class="alert alert-warning" role="alert">
                <?php echo htmlspecialchars($directoryError, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive shadow-sm rounded">
            <table class="table table-striped table-hover align-middle mb-0 directory-table">
                <thead class="table-primary">
                    <tr>
                        
                        <th scope="col">Head of Office</th>
                        <th scope="col">Office / Department</th>
                        <th scope="col">Position</th>
                        <th scope="col">Contact</th>
                        <th scope="col">Email</th>
                        <th scope="col">Facebook</th>
                    </tr>
                </thead>
                <tbody id="directoryTableBody">
                    <?php if (empty($groupedDirectory)) : ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Directory information is currently unavailable.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($groupedDirectory as $office => $entries) : ?>
                            
                            <?php foreach ($entries as $entry) :
                                
                                $head = $entry['HEAD OF OFFICE'] ?? '';
                                $officeName = $entry['OFFICE/DEPARTMENT'] ?? '';
                                $position = $entry['POSITION'] ?? '';
                                $contact = $entry['CONTACT NUMBER'] ?? '';
                                $email = $entry['EMAIL'] ?? '';
                                $facebook = $entry['FACEBOOK PAGE'] ?? '';

                                $contactLink = directory_format_tel_href($contact);
                                $emailLink = filter_var($email, FILTER_VALIDATE_EMAIL) ? 'mailto:' . $email : null;
                                $facebookLink = directory_normalize_url($facebook);

                                $searchParts = array_filter([
                                    $head,
                                    $officeName,
                                    $position,
                                    $contact,
                                    $email,
                                    $facebook,
                                ]);
                                $searchAttr = htmlspecialchars(strtolower(implode(' ', $searchParts)), ENT_QUOTES, 'UTF-8');
                                ?>
                                <tr data-search="<?php echo $searchAttr; ?>">
                                    <td><?php echo htmlspecialchars($head, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($officeName !== '' ? $head : 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($position !== '' ? $position : 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <?php if ($contactLink !== null) : ?>
                                            <a href="<?php echo htmlspecialchars($contactLink, ENT_QUOTES, 'UTF-8'); ?>" class="text-decoration-none">
                                                <i class="bi bi-telephone me-1"></i><?php echo htmlspecialchars($contact, ENT_QUOTES, 'UTF-8'); ?>
                                            </a>
                                        <?php elseif ($contact !== '') : ?>
                                            <?php echo htmlspecialchars($contact, ENT_QUOTES, 'UTF-8'); ?>
                                        <?php else : ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($emailLink !== null) : ?>
                                            <a href="<?php echo htmlspecialchars($emailLink, ENT_QUOTES, 'UTF-8'); ?>" class="text-decoration-none">
                                                <i class="bi bi-envelope me-1"></i><?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>
                                            </a>
                                        <?php elseif ($email !== '') : ?>
                                            <?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>
                                        <?php else : ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($facebookLink !== null) : ?>
                                            <a href="<?php echo htmlspecialchars($facebookLink, ENT_QUOTES, 'UTF-8'); ?>" class="text-decoration-none" target="_blank" rel="noopener">
                                                <i class="bi bi-facebook me-1"></i>Visit Page
                                            </a>
                                        <?php elseif ($facebook !== '') : ?>
                                            <?php echo htmlspecialchars($facebook, ENT_QUOTES, 'UTF-8'); ?>
                                        <?php else : ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<style>
    .directory-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
    }

    .directory-table tbody td {
        vertical-align: top;
        white-space: normal;
        word-break: break-word;
    }

    .directory-table .table-group-header td {
        background-color: #f8f9fa;
    }

    @media (max-width: 767.98px) {
        .directory-table thead th {
            font-size: 0.75rem;
        }

        .directory-table tbody td {
            font-size: 0.85rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var searchInput = document.getElementById('directorySearch');
        var rows = Array.prototype.slice.call(document.querySelectorAll('#directoryTableBody tr[data-search]'));

        searchInput.addEventListener('input', function () {
            var query = this.value.trim().toLowerCase();

            rows.forEach(function (row) {
                var matches = query === '' || row.getAttribute('data-search').indexOf(query) !== -1;
                row.style.display = matches ? '' : 'none';
            });

            var groupHeaders = document.querySelectorAll('#directoryTableBody tr.table-group-header');
            groupHeaders.forEach(function (header) {
                var next = header.nextElementSibling;
                var hasVisible = false;
                while (next && !next.classList.contains('table-group-header')) {
                    if (next.style.display !== 'none') {
                        hasVisible = true;
                        break;
                    }
                    next = next.nextElementSibling;
                }
                header.style.display = hasVisible ? '' : 'none';
            });
        });
    });
</script>

<?php include 'footer.php';
?>