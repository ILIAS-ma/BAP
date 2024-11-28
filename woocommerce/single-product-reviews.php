<?php
/**
 * Custom Template for Product Reviews
 */

defined('ABSPATH') || exit;

global $product;

// Ensure comments are open for the product
if (!comments_open()) {
    return;
}

$comments = get_comments(array('post_id' => $product->get_id()));
?>

<div id="reviews" class="woocommerce-Reviews">
    <div id="comments">
        <h2 class="woocommerce-Reviews-title">VOS AVIS</h2>

        <?php if (have_comments()) : ?>
            <div class="reviews-container">
                <?php foreach ($comments as $comment) : ?>
                    <div class="review-card">
                        <!-- Display Reviewer Name -->
                        <h3 class="review-author">
                            <?php echo esc_html(get_comment_meta($comment->comment_ID, 'reviewer_name', true)) ?: 'Anonyme'; ?>
                        </h3>

                        <!-- Display Review Title -->
                        <h4 class="review-title">
                            <?php echo esc_html(get_comment_meta($comment->comment_ID, 'title', true)) ?: 'Sans titre'; ?>
                        </h4>

                        <!-- Display Review Text -->
                        <p class="review-text">
                            <?php echo esc_html($comment->comment_content); ?>
                        </p>

                        <!-- Display Review Rating -->
                        <div class="review-meta">
                            <span class="review-stars">
                                <?php
                                $rating = intval(get_comment_meta($comment->comment_ID, 'rating', true));
                                echo wc_get_rating_html($rating); // Display star rating.
                                ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="woocommerce-noreviews">Il n'y a pas encore d'avis.</p>
        <?php endif; ?>
    </div>

    <?php if (get_option('woocommerce_enable_review_rating') === 'yes') : ?>
        <div id="review_form_wrapper">
            <div id="review_form">
                <?php
                $comment_form = array(
                    'title_reply'         => 'Laissez un avis',
                    'comment_notes_after' => '',
                    'fields'              => array(
                        'reviewer_name' => '<p class="comment-form-reviewer-name"><label for="reviewer_name">Votre prénom <span class="required">*</span></label>' .
                                           '<input id="reviewer_name" name="reviewer_name" type="text" value="" size="30" required /></p>',
                        'email'         => '<p class="comment-form-email"><label for="email">Votre email (optionnel)</label>' .
                                           '<input id="email" name="email" type="email" value="" size="30" /></p>',
                    ),
                    'comment_field'       => '<p class="comment-form-title"><label for="title">Titre de l\'avis</label>' .
                                              '<input id="title" name="title" type="text" value="" size="30" /></p>' .
                                              '<p class="comment-form-comment"><label for="comment">Votre commentaire <span class="required">*</span></label>' .
                                              '<textarea id="comment" name="comment" cols="45" rows="8" required></textarea></p>' .
                                              '<p class="comment-form-rating"><label for="rating">Votre note</label>' .
                                              '<select name="rating" id="rating" required>
                                                <option value="">Évaluer...</option>
                                                <option value="5">★★★★★</option>
                                                <option value="4">★★★★☆</option>
                                                <option value="3">★★★☆☆</option>
                                                <option value="2">★★☆☆☆</option>
                                                <option value="1">★☆☆☆☆</option>
                                              </select></p>',
                    'label_submit'        => 'Soumettre',
                    'logged_in_as'        => '',
                );

                // Use WooCommerce's comment form to apply the customizations
                comment_form($comment_form);
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>
