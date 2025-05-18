<?php
session_start();
error_reporting(0);
include('includes/config.php');


//ovde je isto ajax za ucitavanje liste za sobe koju koristim dole
if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
	$lastId = isset($_GET['last_id']) ? intval($_GET['last_id']) : 0;
	$sql = "SELECT * FROM tbltourpackages WHERE PackageId > :lastid ORDER BY PackageId ASC";
	$stmt = $dbh->prepare($sql);
	$stmt->bindParam(':lastid', $lastId, PDO::PARAM_INT);
	$stmt->execute();
	$newPackages = $stmt->fetchAll(PDO::FETCH_ASSOC);
	header('Content-Type: application/json');
	echo json_encode($newPackages);
	exit;
}
?>
<!DOCTYPE HTML>
<html>

<head>
	<title>TMS | Package List</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="applijewelleryion/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
	<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
	<link href="css/style.css" rel='stylesheet' type='text/css' />
	<link href='//fonts.googleapis.com/css?family=Open+Sans:400,700,600' rel='stylesheet' type='text/css'>
	<link href='//fonts.googleapis.com/css?family=Roboto+Condensed:400,700,300' rel='stylesheet' type='text/css'>
	<link href='//fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'>
	<link href="css/font-awesome.css" rel="stylesheet">

	<script src="js/jquery-1.12.0.min.js"></script>
	<script src="js/bootstrap.min.js"></script>

	<link href="css/animate.css" rel="stylesheet" type="text/css" media="all">
	<script src="js/wow.min.js"></script>
	<script>
		new WOW().init();
	</script>


	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
		function ucitajNovosti() {
			$.getJSON("novosti.json", function(data) {
				let tekst = data.novosti.map(item => item.tekst).join(" ⚫ ");
				$("#novostiText").text(tekst);
			}).fail(function() {
				$("#novostiText").text("Greška pri učitavanju novosti.");
			});
		}

		// Učitavanje odmah i na svakih 2 sekunde
		ucitajNovosti();
		setInterval(ucitajNovosti, 2000);
	</script>

	<style>
		.novosti-bar {
			background-color: #f0f0f0;
			padding: 10px;
			margin-top: 10px;
			border-top: 2px solid #ccc;
			border-bottom: 2px solid #ccc;
			font-weight: bold;
			font-size: 16px;
			color: #333;
		}
	</style>
</head>

<body>
	<?php include('includes/header.php'); ?>

	<div class="banner-3">
		<div class="container">
			<h1 class="wow zoomIn animated animated" data-wow-delay=".5s" style="visibility: visible; animation-delay: 0.5s; animation-name: zoomIn;"> TMS- Package List</h1>
		</div>
		<div class="novosti-bar" id="novostiBar">
			<marquee behavior="scroll" direction="left" id="novostiText">Učitavanje novosti...</marquee>
		</div>
	</div>

	<div class="rooms">
		<div class="container">
			<div class="room-bottom">
				<h3>Package List</h3>

				<?php
				$sql = "SELECT * FROM tbltourpackages ORDER BY PackageId ASC";
				$query = $dbh->prepare($sql);
				$query->execute();
				$results = $query->fetchAll(PDO::FETCH_OBJ);
				$lastId = 0;
				foreach ($results as $result) {
					$lastId = $result->PackageId;
				?>
					<div class="rom-btm">
						<div class="col-md-3 room-left wow fadeInLeft animated" data-wow-delay=".5s">
							<img src="admin/pacakgeimages/<?php echo htmlentities($result->PackageImage); ?>" class="img-responsive" alt="">
						</div>
						<div class="col-md-6 room-midle wow fadeInUp animated" data-wow-delay=".5s">
							<h4>Package Name: <?php echo htmlentities($result->PackageName); ?></h4>
							<h6>Package Type : <?php echo htmlentities($result->PackageType); ?></h6>
							<p><b>Package Location :</b> <?php echo htmlentities($result->PackageLocation); ?></p>
							<p><b>Features</b> <?php echo htmlentities($result->PackageFetures); ?></p>
						</div>
						<div class="col-md-3 room-right wow fadeInRight animated" data-wow-delay=".5s">
							<h5>USD <?php echo htmlentities($result->PackagePrice); ?></h5>
							<a href="package-details.php?pkgid=<?php echo htmlentities($result->PackageId); ?>" class="view">Details</a>
						</div>
						<div class="clearfix"></div>
					</div>
				<?php } ?>

			</div>
		</div>
	</div>
	<script>
		let lastId = <?php echo $lastId; ?>;

		setInterval(function() {
			fetch('packages.php?ajax=1&last_id=' + lastId)
				.then(response => response.json())
				.then(data => {
					data.forEach(pkg => {
						const html = `
                        <div class="rom-btm" data-id="${pkg.PackageId}">
                            <div class="room-left">
                                <img src="admin/pacakgeimages/${pkg.PackageImage}" alt="">
                            </div>
                            <div class="room-midle">
                                <h4>Package Name: ${pkg.PackageName}</h4>
                                <h6>Package Type: ${pkg.PackageType}</h6>
                                <p><b>Package Location :</b> ${pkg.PackageLocation}</p>
                                <p><b>Features:</b> ${pkg.PackageFetures}</p>
                            </div>
                            <div class="room-right">
                                <h5>USD ${pkg.PackagePrice}</h5>
                                <a href="package-details.php?pkgid=${pkg.PackageId}" class="view">Details</a>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    `;
						document.querySelector('.room-bottom').insertAdjacentHTML('beforeend', html);
						lastId = pkg.PackageId;
					});
				});
		}, 2000);
	</script>

	<?php include('includes/footer.php'); ?>
	<?php include('includes/signup.php'); ?>
	<?php include('includes/signin.php'); ?>
	<?php include('includes/write-us.php'); ?>
</body>

</html>