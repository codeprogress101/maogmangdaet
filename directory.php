<?php
$page_title = 'About Daet';
$activePage = 'about';
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
 
            <!-- ================= NAVBAR ================= -->
            

            <!-- HERO SECTION -->
            <header class="about-header text-white text-center d-flex align-items-center justify-content-center"
            style="background: url(assets/img/aboutdaet.png); height: 40vh;">
            <div class="container px-4 px-lg-5">
                <h1 class="fw-bold display-4">MUNICIPAL DIRECTORY OF DAET</h1>
            </div>
            </header>

          
<!-- INTRO SECTION -->
<section class="about-daet py-5" style="background-color: #fff;">
  <div class="container px-4 px-lg-5">
    
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item"><a href="about.php">Know Daet</a></li>
        <li class="breadcrumb-item active" aria-current="page">Municipal Directory</li>
      </ol>
    </nav>

    <div class="row gx-5"> <!-- Added gx-5 for spacing between columns -->
      
      <!-- Left Column -->
      <div class="about-left col-lg-8">
       
        <p class="text-muted mb-5">
          Learn more about the history, culture, and governance of Daet.
        </p>

      </div>

          <!-- Search box -->
<div class="d-flex justify-content-end mb-3">
  <input type="text" id="searchInput" class="form-control" style="max-width:250px;" placeholder="Search...">
</div>

<!-- Directory Table -->
<div class="table-responsive">
  <table class="table table-striped table-hover align-middle shadow-sm">
    <thead class="table-primary">
      <tr>
        <th scope="col">Name / Designation</th>
        <th scope="col">Department / Office</th>
        <th scope="col">Telephone No.</th>
      </tr>
    </thead>
    <tbody id="directoryTable">
      <tr>
        <td>
          <strong>Rossano C. Valencia</strong><br>
          <small class="text-muted">Municipal Mayor</small>
        </td>
        <td>Office of the Municipal Mayor</td>
        <td>09123456789</td>
      </tr>
      <tr>
        <td>
          <strong>Benito S. Ochoa</strong><br>
          <small class="text-muted">Acting Vice Mayor</small>
        </td>
        <td>Office of the Municipal Vice Mayor</td>
        <td>09123456789</td>
      </tr>
      <tr>
        <td>
          <strong>Raychel B. Valencia</strong><br>
          <small class="text-muted">Municipal Administrator</small>
        </td>
        <td>Municipal Administrator’s Office</td>
        <td>09123456789</td>
      </tr>
      <!-- Keep adding rows here -->
    </tbody>
  </table>
</div>

<!-- Search Script -->
<script>
  document.getElementById("searchInput").addEventListener("keyup", function () {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("#directoryTable tr");

    rows.forEach(row => {
      let text = row.textContent.toLowerCase();
      row.style.display = text.includes(filter) ? "" : "none";
    });
  });
</script>


      
    </div>
  </div>
</section>




  

<?php include 'footer.php'; ?>