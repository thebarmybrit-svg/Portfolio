<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Alexander&apos;s Portfolio - <?= htmlspecialchars($heading) ?></title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="ROBOTS" content="NOINDEX,NOFOLLOW">
        <meta name="description" content="<?= htmlspecialchars($description) ?>">
        <link rel="stylesheet" href="../js/pageCrossfade/pagecrossfade.css">
        <link rel="stylesheet" href="../css/styles.css">
    </head>

    <body>
        <div id="header">
            <header>
                <div class="nav-toggle hidden-sm hidden-md hidden-lg hidden-xl">
                    <a class="btn btn--toggle">
                        <span class="icon icon-menu"></span>
                    </a>
                </div>
                <div class="inner">
                    <div class="img-container">
                        <img class="img img--banner" src="../img/data-600x243.jpg" alt="neon blue 1s and 0s on a dark background">
                    </div>
                    <div class="content">
                        <div class="header-container">
                            <div class="container">
                                <div class="grid">
                                    <h1 class="typewriter-static"><?= htmlspecialchars($heading) ?></h1>
                                    <a class="btn btn--nav" href="<?= htmlspecialchars($scroll) ?>">
                                        Scroll Down
                                        <em class="icon icon-circle-down"></em>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
        </div>