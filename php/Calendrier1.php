<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dynamic Calendar</title>
    <link rel="stylesheet" href="../css/styles_cld.css">
</head>

<body>
    <?php include ("header.php"); ?>

    <div class="calendar">
        <div class="calendar-header">
            <button id="prev-month">‹</button>
            <div id="month-year"></div>
            <button id="next-month">›</button>
        </div>
        <div class="calendar-body">
            <div class="calendar-weekdays">
                <div>Dim</div>
                <div>Lun</div>
                <div>Mar</div>
                <div>Mer</div>
                <div>Jeu</div>
                <div>Ven</div>
                <div>Sam</div>
            </div>
            <div class="calendar-dates">

            </div>
        </div>
    </div>
    <div id="crenaux">
    <div id="selected-date">

    </div>
    <div class="hour-calendar" id="hour-calendar">

    </div>
    </div>
    <?php include ("fotter.php"); ?>
    <script src="../js/Calendrier.js"></script>
</body>

</html>