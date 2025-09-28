<?php
$page_title = 'Maogmang Daet';
$activePage = 'home';
include 'header.php';

function formatFileTitle(string $filePath): string
{
    $fileName = pathinfo($filePath, PATHINFO_FILENAME);
    $spaced = preg_replace('/[._-]+/', ' ', $fileName);

    return ucwords(trim((string) $spaced));
}

function buildDocumentList(array $files, string $descriptionTemplate): array
{
    $items = [];

    foreach (array_slice($files, 0, 2) as $index => $filePath) {
        $items[] = [
            'title' => formatFileTitle($filePath),
            'description' => sprintf($descriptionTemplate, $index + 1),
            'file_path' => $filePath,
        ];
    }

    return $items;
}

$executiveFiles = [
    'assets/uploads/CC-2023-UPDATED-NEW.1111.pdf',
    'assets/uploads/RESOLUTION-NO.003-2021.MUNICIPAL.ORDINANCE.NO_.393-2021.pdf',
    'assets/uploads/RESOLUTION-NO.051-2021.MUNICIPAL.ORDINANCE.NO_.398-2021.pdf',
];

$hearingFiles = [
    'assets/uploads/RESOLUTION-NO.062-2021.MUNICIPAL.ORDINANCE.NO_.400-2021.pdf',
    'assets/uploads/RESOLUTION-NO.077-2021.MUNICIPAL.ORDINANCE.NO_.401-2021.pdf',
    'assets/uploads/RESOLUTION-NO.096-2021.MUNICIPAL.ORDINANCE.NO_.402-2021.pdf',
];

$ordinanceFiles = [
    'assets/uploads/RESOLUTION-NO.097-2021.MUNICIPAL.ORDINANCE.NO_.403-2021.pdf',
    'assets/uploads/RESOLUTION-NO.098-2021.MUNICIPAL.ORDINANCE.NO_.404-2021.pdf',
    'assets/uploads/RESOLUTION-NO.099-2021.MUNICIPAL.ORDINANCE.NO_.405-2021.pdf',
];

$resolutionFiles = [
    'assets/uploads/RESOLUTION-NO.108-2021.MUNICIPAL.ORDINANCE.NO_.406-2021.pdf',
    'assets/uploads/RESOLUTION-NO.114-2021.APPROPRIATION.ORDINANCE.NO_.01-2021.pdf',
    'assets/uploads/RESOLUTION-NO.122-2021.MUNICIPAL.ORDINANCE.NO_.407-2021.pdf',
];

$executiveIssuances = buildDocumentList(
    $executiveFiles,
    'Placeholder description for Executive Issuance %d. Details will be managed via the admin panel.'
);

$publicHearings = buildDocumentList(
    $hearingFiles,
    'Placeholder description for Public Hearing %d. Supporting documents will be uploaded by the admin panel.'
);

$recentOrdinances = buildDocumentList(
    $ordinanceFiles,
    'Placeholder description for Ordinance %d. Additional context will be supplied later.'
);

$resolutions = buildDocumentList(
    $resolutionFiles,
    'Placeholder description for Resolution %d. More information will be available soon.'
);

$announcements = [
    [
        'title' => 'Community Cleanup Drive',
        'description' => 'Join the town-wide cleanup along major thoroughfares this Saturday. Gloves and sacks will be provided.',
    ],
    [
        'title' => 'Water Service Advisory',
        'description' => 'Expect intermittent water supply in Barangay Camambugan from 9:00 AM to 4:00 PM due to line maintenance.',
    ],
    [
        'title' => 'Scholarship Application Reminder',
        'description' => 'Municipal scholarship applications are open until June 30. Submit requirements at the Mayor’s Office.',
    ],
];
?>

 <!-- Masthead-->
    <header class="masthead">
      <div
        class="container px-4 px-lg-5 d-flex h-100 align-items-center justify-content-center"
      >
        <div class="d-flex justify-content-center">
          <div class="text-center">
        <div class="masthead-button">
              <a class="btn btn-primary" href="#about">Get Started</a>
            </div>
          </div>
        </div>
      </div>
    </header>


    <!-- About-->
    <section class="about-section text-center" id="about">
      <video autoplay muted loop playsinline id="aboutVideo">
        <source src="assets/video/BACKGROUND VIDEO.mp4" type="video/mp4" />
        Your browser does not support the video tag.
      </video>
      <div class="container px-4 px-lg-5">
        <div class="row gx-4 gx-lg-5 justify-content-center">
          <div class="col-lg-8">
            <h2 class="text-white mb-4">
              W E L C O M E &nbsp T O &nbsp D A E T
            </h2>
            <p>
              Ang Bayan ng Daet ang kabisera ng lalawigan ng Camarines Norte, na
              nagsisilbing sentro ng pamahalaan, kalakalan, at edukasyon sa
              rehiyon. Kilala ang bayan sa kasaysayan bilang tahanan ng
              kauna-unahang bantayog para kay Dr. Jose Rizal sa bansa, na
              sumasalamin sa makabayang kultura ng mga mamamayan nito.
            </p>
          </div>
        </div>

        </div>
    </section>


<!-- WHAT'S NEW SECTION -->
<section
  class="updates-section bg-light py-4"
  id="updates"
  style="min-height: 100vh"
>
  <div class="container px-4 px-lg-5">
    <!-- Section Title -->
    <h2
      class="text-center mb-4 animate-on-scroll"
      data-animate="animate__fadeInDown"
    >
      What's New in Daet?
    </h2>

   

    <div class="row">
      <!-- Left column -->
      <div class="col-lg-8">
        <!-- Executive Issuances -->
        <div class="mb-3 animate-on-scroll" data-animate="animate__fadeInLeft">
          <h6 class="fw-bold mb-1">Executive Issuances</h6>
          <div class="list-group shadow-sm small">
            <?php if (!empty($executiveIssuances)): ?>
              <?php foreach ($executiveIssuances as $document): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <div class="fw-semibold">
                      <?php echo htmlspecialchars($document['title'] ?? 'Untitled'); ?>
                    </div>
                    <p class="text-muted small mb-0">
                      <?php echo htmlspecialchars($document['description'] ?? 'No description.'); ?>
                    </p>
                  </div>
                  <div>
                    <?php if (!empty($document['file_path'])): ?>
                      <a href="<?php echo htmlspecialchars($document['file_path']); ?>" class="btn btn-primary btn-sm" target="_blank">Preview</a>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="list-group-item text-muted">No records.</div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Public Hearings -->
        <div class="mb-3 animate-on-scroll" data-animate="animate__fadeInRight">
          <h6 class="fw-bold mb-1">Public Hearings</h6>
          <div class="list-group shadow-sm small">
            <?php if (!empty($publicHearings)): ?>
              <?php foreach ($publicHearings as $hearing): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <div class="fw-semibold">
                      <?php echo htmlspecialchars($hearing['title'] ?? 'Untitled'); ?>
                    </div>
                    <p class="text-muted small mb-0">
                      <?php echo htmlspecialchars($hearing['description'] ?? 'No description.'); ?>
                    </p>
                  </div>
                  <div>
                    <?php if (!empty($hearing['file_path'])): ?>
                      <a href="<?php echo htmlspecialchars($hearing['file_path']); ?>" class="btn btn-outline-primary btn-sm" target="_blank">Preview</a>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="list-group-item text-muted">No records.</div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Ordinances -->
        <div class="mb-3 animate-on-scroll" data-animate="animate__fadeInLeft">
          <h6 class="fw-bold mb-1">Recent Ordinances</h6>
          <div class="list-group shadow-sm small">
            <?php if (!empty($recentOrdinances)): ?>
              <?php foreach ($recentOrdinances as $ordinance): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <div class="fw-semibold">
                      <?php echo htmlspecialchars($ordinance['title'] ?? 'Untitled'); ?>
                    </div>
                    <p class="text-muted small mb-0">
                      <?php echo htmlspecialchars($ordinance['description'] ?? 'No description.'); ?>
                    </p>
                  </div>
                  <div>
                    <?php if (!empty($ordinance['file_path'])): ?>
                      <a href="<?php echo htmlspecialchars($ordinance['file_path']); ?>" class="btn btn-primary btn-sm" target="_blank">Preview</a>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="list-group-item text-muted">No records.</div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Resolutions -->
        <div class="mb-3 animate-on-scroll" data-animate="animate__fadeInRight">
          <h6 class="fw-bold mb-1">Resolutions</h6>
          <div class="list-group shadow-sm small">
            <?php if (!empty($resolutions)): ?>
              <?php foreach ($resolutions as $resolution): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <div class="fw-semibold">
                      <?php echo htmlspecialchars($resolution['title'] ?? 'Untitled'); ?>
                    </div>
                    <p class="text-muted small mb-0">
                      <?php echo htmlspecialchars($resolution['description'] ?? 'No description.'); ?>
                    </p>
                  </div>
                  <div>
                    <?php if (!empty($resolution['file_path'])): ?>
                      <a href="<?php echo htmlspecialchars($resolution['file_path']); ?>" class="btn btn-outline-primary btn-sm" target="_blank">Preview</a>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="list-group-item text-muted">No records.</div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Right column -->
      <div class="col-lg-4">
        <!-- Weather -->
        <div class="card text-center mb-3 animate-on-scroll" data-animate="animate__fadeInUp">
          <div class="card-body bg-primary text-white p-3">
            <h6 class="card-title mb-2">Daet Weather</h6>
            <div id="weather-today"><p class="mb-2">Loading...</p></div>
            <div id="weather-forecast" class="d-flex justify-content-around small flex-wrap"></div>
          </div>
        </div>

        <!-- Map -->
        <div class="card mb-3 animate-on-scroll" data-animate="animate__fadeInUp">
          <div class="card-header bg-primary text-white text-center py-2">📍 Daet</div>
          <div class="ratio ratio-16x9">
            <iframe src="https://www.google.com/maps?q=Daet,Camarines%20Norte&output=embed" style="border:0" allowfullscreen loading="lazy"></iframe>
          </div>
        </div>

        <!-- Announcements -->
        <div class="mb-3 animate-on-scroll" data-animate="animate__fadeInUp">
          <h6 class="fw-bold mb-2">Announcements</h6>
          <div class="list-group shadow-sm small">
            <?php foreach ($announcements as $announcement): ?>
              <div class="list-group-item border-start border-4 border-warning">
                <div class="fw-semibold text-warning mb-1"><?php echo htmlspecialchars($announcement['title']); ?></div>
                <p class="text-muted small mb-0"><?php echo htmlspecialchars($announcement['description']); ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Socials -->
        <div class="text-center animate-on-scroll" data-animate="animate__fadeInUp">
          <h6>Follow Us</h6>
          <a href="https://www.facebook.com/lgudaet" target="_blank" class="social-icon"><i class="fab fa-facebook-f"></i></a>
          <a href="https://twitter.com/lgudaet" target="_blank" class="social-icon"><i class="fab fa-twitter"></i></a>
          <a href="https://www.instagram.com/lgudaet" target="_blank" class="social-icon"><i class="fab fa-instagram"></i></a>
          <a href="https://www.youtube.com/@lgudaet" target="_blank" class="social-icon"><i class="fab fa-youtube"></i></a>
        </div>
      </div>
    </div>
  </div>
</section>





    <!--News Projects-->
    <section class="projects-section bg-light" id="projects">
      <div class="container px-4 px-lg-5">
        <h2 class="mx-auto mt-2 mb-5">News and Updates</h2>
        <!-- Featured News Row-->
        <div class="row gx-0 mb-4 mb-lg-5 align-items-center">
          <div class="col-xl-8 col-lg-7">
            <img
              class="img-fluid mb-3 mb-lg-0"
              src="assets/img/news1.png"
              alt="Mangrove Planting"
            />
          </div>
          <div class="col-xl-4 col-lg-5">
            <div class="featured-text text-center text-lg-left">
              <h4>𝐌𝐀𝐍𝐆𝐑𝐎𝐕𝐄 𝐓𝐑𝐄𝐄 𝐏𝐋𝐀𝐍𝐓𝐈𝐍𝐆 𝐀𝐂𝐓𝐈𝐕𝐈𝐓𝐘</h4>
              <p class="text-black-50 mb-0">
                Isinagawa po natin ngayong umaga ang Mangrove Tree Planting
                Activity sa Bagasbas Beach katuwang ang Municipal Agriculture
                Office, Philippine Coast Guard, Municipal Rural Improvement Club
                (MRIC) at BAEW, bilang isang mahalagang hakbang para sa
                pangangalaga ng ating kapaligiran at kalikasan. Ang pagtatanim
                po ng bakawan ay napakahalaga sapagkat ito ay nagsisilbing
                depensa laban sa malalakas na alon at storm surge. ng inisyatibo
                ay nagsisilbing inspirasyon sa ating lahat upang maging mas
                responsable sa pangangalaga ng kalikasan para sa kasalukuyan at
                sa mga susunod na henerasyon.
              </p>

               <a href="full-article.php" class="text-primary">See more...</a>
            </div>
          </div>
        </div>
        <!-- News One Row-->
        <div class="row gx-0 mb-5 mb-lg-0 justify-content-center">
          <div class="col-lg-6">
            <img class="img-fluid" src="assets/img/news2.JPG" alt="..." />
          </div>
          <div class="col-lg-6">
            <div class="bg-black text-center h-100 project">
              <div class="d-flex h-100">
                <div
                  class="project-text w-100 my-auto text-center text-lg-left"
                >
                  <h4 class="text-white">
                    𝐃𝐢𝐬𝐭𝐫𝐢𝐛𝐮𝐭𝐢𝐨𝐧 𝐨𝐟 𝐂𝐨𝐦𝐩𝐥𝐞𝐭𝐞 𝐅𝐞𝐫𝐭𝐢𝐥𝐢𝐳𝐞𝐫 𝐚𝐧𝐝 𝐔𝐫𝐞𝐚
                  </h4>
                  <p class="mb-0 text-white-50">
                    Bilang bahagi ng patuloy na suporta sa sektor ng
                    agrikultura, isinagawa ng Lokal na Pamahalaan ng Daet ang
                    unang pamamahagi ng kabuuang (900) fertilizers at urea sa
                    ating mga magsasaka. Ang mga beneficiaries ngayong araw ay
                    mula sa Brgy. Alawihao at Lag-on. Layunin ng programang ito
                    na mapabuti ang ani, mapataas ang kita, at matiyak ang sapat
                    na suplay ng pagkain para sa komunidad. Katuwang ang
                    Municipal Agriculture Office, patuloy na magsasagawa ng mga
                    ganitong inisyatiba ang LGU upang matulungan ang ating mga
                    magsasaka.
                  </p>

                <a href="full-article.php" class="text-primary"
                    >See more...</a
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- News Two Row-->
        <div class="row gx-0 justify-content-center">
          <div class="col-lg-6">
            <img class="img-fluid" src="assets/img/news3.JPG" alt="..." />
          </div>
          <div class="col-lg-6 order-lg-first">
            <div class="bg-black text-center h-100 project">
              <div class="d-flex h-100">
                <div
                  class="project-text w-100 my-auto text-center text-lg-right"
                >
                  <h4 class="text-white">
                    𝟏𝟐𝟓𝐓𝐇 𝐏𝐇𝐈𝐋𝐈𝐏𝐏𝐈𝐍𝐄 𝐂𝐈𝐕𝐈𝐋 𝐒𝐄𝐑𝐕𝐈𝐂𝐄 𝐀𝐍𝐍𝐈𝐕𝐄𝐑𝐒𝐀𝐑𝐘 𝐎𝐏𝐄𝐍𝐈𝐍𝐆 𝐏𝐀𝐑𝐀𝐃𝐄
                  </h4>
                  <p class="mb-0 text-white-50">
                    Opisyal pong binuksan kahapon ang pagdiriwang ng Ika-125
                    Anibersaryo ng Philippine Civil Service na may temang “Bawat
                    Kawani, Lingkod Bayani: Puso, Dangal at Galing para sa
                    Bayan.” Layunin ng temang ito na kilalanin at ipagdiwang ang
                    dedikasyon at propesyonalismo ng mga lingkod-bayan. Kasama
                    rin po natin sa aktibidad na ito sina Acting Governor Joseph
                    Ascutia at Provincial Administator Don Padilla at iba’t-
                    ibang sektor ng pamahalaan at mga kapwa natin lingkod bayan.
                  </p>

        <a href="full-article.php" class="text-primary"
                    >See more...</a
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

   <!-- OPPORTUNITIES SECTION -->
    <section
      class="opportunities-section d-flex flex-column justify-content-center align-items-center text-center"
      id="opportunities"
      style="min-height: 100vh; background-color: #f8f9fa"
    >
      <div class="container">
        <!-- Title -->
        <h2 class="mb-4 animate__animated animate__fadeInDown">
          STARTUPS & OTHER OPPORTUNITIES
        </h2>

        <!-- Video -->
        <div class="video-container mb-4 animate__animated animate__fadeInUp">
          <video
            id="opportunityVideo"
            loop
            playsinline
            class="w-100 rounded shadow"
          >
            <source src="assets/video/Seminar.mp4" type="video/mp4" />
            Your browser does not support the video tag.
          </video>
        </div>

        <!-- Button -->
        <a
          href="#more-opportunities"
          class="btn btn-primary btn-lg animate__animated animate__fadeInUp animate__delay-1s"
        >
          Learn More
        </a>
      </div>
    </section>

    <section class="daet-section" id="daet">
      <div class="container">
        <h5 class="section-subtitle">
          THE CAPITAL TOWN OF THE GATEWAY TO BICOLANDIA
        </h5>
        <h2 class="section-title">MAOGMANG DAET</h2>
        <p class="section-desc">
          The Municipality of Daet is a vibrant town known for its history,
          culture, and warm community spirit. Famous for the first Rizal
          monument, its golden sandy beaches, and delicious local delicacies,
          Daet is a blend of heritage and modern progress, making it a unique
          place to explore in Camarines Norte.
        </p>

        <!-- Gallery -->
        <div class="gallery-layout">
          <!-- Large Image -->
          <div class="gallery-large">
            <img src="assets/img/surf.jpg" alt="Daet Large" id="large-photo" />
          </div>

          <!-- Small Images -->
          <div class="gallery-small">
            <img src="assets/img/daet2.jpg" alt="Daet Small" />
            <img src="assets/img/daet3.jpg" alt="Daet Small" />
            <img src="assets/img/daet4.jpg" alt="Daet Small" />
            <img src="assets/img/daet5.jpg" alt="Daet Small" />
          </div>
        </div>

        <!-- Explore Daet Button -->
        <div class="explore-btn-wrapper">
          <a href="tourism.php" class="explore-btn">Explore Daet</a>
        </div>
      </div>
    </section>

 <!-- Emergency Hotlines -->
    <section class="hotlines-section">
      <div class="container px-4 px-lg-5">
        <!-- Section Title -->
        <h2 class="hotlines-title text-center">EMERGENCY HOTLINES</h2>

        <div class="row gx-4 gx-lg-5 text-center">
          <!-- Mobile -->
          <div class="col-md-4 mb-4">
            <i class="fas fa-mobile-alt hotline-icon"></i>
            <h4 class="hotline-heading">Mobile (COMCEN)</h4>
            <p>+63 908-525-3000 | Smart</p>
            <p>+63 963-220-9700 | TNT</p>
          </div>

          <!-- Landline -->
          <div class="col-md-4 mb-4">
            <i class="fas fa-phone-alt hotline-icon"></i>
            <h4 class="hotline-heading">Landline (COMCEN)</h4>
            <p>(054) 472-3000</p>
            <p>(054) 8712050 local 3000 (PLDT)</p>
          </div>

          <!-- Fire Department -->
          <div class="col-md-4 mb-4">
            <i class="fas fa-fire-alt hotline-icon"></i>
            <h4 class="hotline-heading">Bureau of Fire Protection</h4>
            <p>(+63) 923-0839429 | (054) 473-8472</p>
            <p>(054) 473-2633 | (054) 8716454</p>
          </div>
        </div>

        <!-- Button -->
        <div class="text-center mt-4">
          <a href="#important-numbers" class="hotlines-btn">
            List of Important Numbers
          </a>
        </div>
      </div>
    </section>

    <?php
$page_footer_scripts = <<<'HTML'
    <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script>
    HTML;
include 'footer.php';
?>