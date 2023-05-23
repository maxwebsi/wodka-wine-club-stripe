<header>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <a class="navbar-brand" href="https://www.selecttasting.com/">
                <img src="img/select-tasting-logo-white.png" alt="Logo Select Tasting">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link border-right"
                           href="https://www.selecttasting.com/brunello-wine-club-montalcino">CLUB BRUNELLO</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link border-right" href="https://www.selecttasting.com/brunello-shop-collectors">COLLECTOR'S
                            SHOP</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link border-right" href="https://www.selecttasting.com/tuscan-olive-oil">OLIVE
                            OIL</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link border-right" href="https://www.selecttasting.com/wine-tour">WINE
                            EXPERIENCES</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            MORE
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="https://www.selecttasting.com/brunello-community-newsletter">JOIN
                                BRUNELLO COMMUNITY</a>
                            <a class="dropdown-item" href="https://www.selecttasting.com/brunello-archives">BRUNELLO
                                ARCHIVES</a>
                            <a class="dropdown-item" href="https://www.selecttasting.com/meet-the-maker">MEET THE
                                MAKER</a>
                            <a class="dropdown-item" href="https://www.selecttasting.com/solidarityevents">SOLIDARITY
                                EVENTS</a>
                            <a class="dropdown-item" href="https://www.selecttasting.com/shipping-information-page">SHIPPING
                                INFORMATION</a>
                            <a class="dropdown-item" href="https://www.selecttasting.com/contact-us">CONTACT US</a>
                        </div>
                    </li>
                </ul>
            </div>
            <div>
                <?php
                if (isset($_COOKIE['tgsub']) && $_COOKIE['tgsub'] == 'logged') {
                    $logged = true;
                } else {
                    $logged = false;
                }
                ?>
                <a href="logout" title="Logout" id="logout" class="<?php echo ($logged ? '' : 'd-none') ?>">
                    <img id="icon-exit" src="img/exit-white.svg" alt="Logout" width="24" height="24">
                </a>
            </div>
        </nav>
    </div>
</header>