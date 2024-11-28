<?php

// Ajout du support WooCommerce pour la compatibilité avec les fonctionnalités du thème
function mytheme_add_woocommerce_support() {
    add_theme_support('woocommerce', array(
        'gallery_thumbnail_image_width' => 150, // Largeur des miniatures de la galerie
        'single_image_width'            => 600, // Largeur de l'image principale du produit
        'thumbnail_image_width'         => 300, // Largeur des vignettes
    ));
    
    // Activer les fonctionnalités de la galerie WooCommerce
    add_theme_support('wc-product-gallery-lightbox');   // Lightbox pour les images
    add_theme_support('wc-product-gallery-slider');     // Slider de la galerie
}
add_action('after_setup_theme', 'mytheme_add_woocommerce_support');

// Enregistrement des styles WooCommerce si le plugin est actif
function mytheme_enqueue_woocommerce_styles() {
    if (class_exists('WooCommerce')) {
        wp_enqueue_style('woocommerce-styles', plugins_url('woocommerce/assets/css/woocommerce.css')); // CSS WooCommerce
        wp_enqueue_script('wc-single-product'); // Scripts pour la page produit
    }
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_woocommerce_styles');

// Supprimer les breadcrumbs (fil d'Ariane) par défaut de WooCommerce
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

// Supprimer l'affichage de la catégorie du produit sur la page produit
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);

// Retirer tous les onglets sauf celui des avis clients
add_filter('woocommerce_product_tabs', 'remove_all_except_reviews_tab', 98);
function remove_all_except_reviews_tab($tabs) {
    if (isset($tabs['description'])) {
        unset($tabs['description']); // Supprimer l'onglet description
    }
    if (isset($tabs['additional_information'])) {
        unset($tabs['additional_information']); // Supprimer l'onglet informations supplémentaires
    }
    return $tabs;
}

// Afficher la description du produit sous le bouton "Ajouter au panier"
function custom_description_position() {
    global $post;
    echo '<div class="custom-product-description">';
    echo apply_filters('the_content', $post->post_content); // Affichage de la description du produit
    echo '</div>';
}
add_action('woocommerce_single_product_summary', 'custom_description_position', 25);

// Personnaliser le titre des avis
add_filter('woocommerce_reviews_title', 'custom_reviews_title', 10, 2);
function custom_reviews_title($reviews_title, $count) {
    return $count == 0 ? 'Aucun avis' : 'Avis';
}

// Modifier la position du prix sous la description
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 25);

// Ajouter des boutons "+" et "-" pour la quantité
function custom_quantity_buttons_script() {
    if (is_product()) {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const qtyFields = document.querySelectorAll('.quantity input.qty');
                qtyFields.forEach(function (input) {
                    const parent = input.parentElement;

                    const minusButton = document.createElement('button');
                    minusButton.textContent = '-';
                    minusButton.classList.add('qty-minus');
                    parent.insertBefore(minusButton, input);

                    const plusButton = document.createElement('button');
                    plusButton.textContent = '+';
                    plusButton.classList.add('qty-plus');
                    parent.appendChild(plusButton);

                    minusButton.addEventListener('click', function (e) {
                        e.preventDefault();
                        if (input.value > 1) input.value = parseInt(input.value) - 1;
                    });

                    plusButton.addEventListener('click', function (e) {
                        e.preventDefault();
                        input.value = parseInt(input.value) + 1;
                    });
                });
            });
        </script>
        <?php
    }
}
add_action('wp_footer', 'custom_quantity_buttons_script');

// Ajouter un slider pour les avis
function enqueue_review_slider_script() {
    if (is_product()) {
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const track = document.querySelector('.slider-track');
                const slides = document.querySelectorAll('.review-slide');
                const prevBtn = document.querySelector('.prev-btn');
                const nextBtn = document.querySelector('.next-btn');
                let currentIndex = 0;

                function updateSliderPosition() {
                    const slideWidth = slides[0].offsetWidth;
                    track.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
                }

                prevBtn.addEventListener('click', function () {
                    if (currentIndex > 0) {
                        currentIndex--;
                        updateSliderPosition();
                    }
                });

                nextBtn.addEventListener('click', function () {
                    if (currentIndex < slides.length - 1) {
                        currentIndex++;
                        updateSliderPosition();
                    }
                });

                window.addEventListener('resize', updateSliderPosition);
            });
        </script>
        <?php
    }
}
add_action('wp_footer', 'enqueue_review_slider_script');
