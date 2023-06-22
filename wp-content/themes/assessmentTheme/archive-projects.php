<?php
get_header();
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
    'post_type'      => 'projects',
    'posts_per_page' => 5,
    'paged'          => $paged
);

$query = new WP_Query($args);
?>

<main class="wrap">
    <section class="content-area content-thin">
        <?php if ($query->have_posts()) : ?>
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <article class="article-full">
                    <header>
                        <h2><?php the_title(); ?></h2>
                        By: <?php the_author(); ?>
                    </header>
                    <?php the_content(); ?>
                </article>
            <?php endwhile; ?>

            <?php
            // Pagination links
            $pagination_args = array(
                'base'    => add_query_arg('paged', '%#%'),
                'format'  => '',
                'current' => max(1, $paged),
                'total'   => $query->max_num_pages,
                'prev_text' => '&laquo; Previous',
                'next_text' => 'Next &raquo;',
            );

            echo paginate_links($pagination_args);
            ?>
        <?php else : ?>
            <article>
                <p>Sorry, no projects found!</p>
            </article>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>
    </section>
    <?php get_sidebar(); ?>
</main>

<?php get_footer(); ?>
