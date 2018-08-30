<?php
/*
Template Name: Sitemap Page
*/
?>
<?php
$et_ptemplate_settings = array();
$et_ptemplate_settings = maybe_unserialize( get_post_meta(get_the_ID(),'et_ptemplate_settings',true) );

$fullwidth = true;
?>
<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

	<?php get_template_part( 'content', 'page' ); ?>

	<article class="et-additional-content">
		<?php get_template_part( 'includes/breadcrumbs', 'single' ); ?>

		<div class="entry-content container clearfix">
			<?php the_content(); ?>
			<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'Fable' ), 'after' => '</div>' ) ); ?>

<?php endwhile; ?>

			<div id="sitemap" class="responsive">
				<div class="sitemap-col">
					<h2><?php esc_html_e('Pages','Fable'); ?></h2>
					<ul id="sitemap-pages"><?php wp_list_pages('title_li='); ?></ul>
				</div> <!-- end .sitemap-col -->

				<div class="sitemap-col">
					<h2><?php esc_html_e('Categories','Fable'); ?></h2>
					<ul id="sitemap-categories"><?php wp_list_categories('title_li='); ?></ul>
				</div> <!-- end .sitemap-col -->

				<div class="sitemap-col<?php if (!$fullwidth) echo ' last'; ?>">
					<h2><?php esc_html_e('Tags','Fable'); ?></h2>
					<ul id="sitemap-tags">
						<?php $tags = get_tags();
						if ($tags) {
							foreach ($tags as $tag) {
								echo '<li><a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '">' . esc_html( $tag->name ) . '</a></li> ';
							}
						} ?>
					</ul>
				</div> <!-- end .sitemap-col -->

				<?php if (!$fullwidth) { ?>
					<div class="clear"></div>
				<?php } ?>

				<div class="sitemap-col<?php if ($fullwidth) echo ' last'; ?>">
					<h2><?php esc_html_e('Authors','Fable'); ?></h2>
					<ul id="sitemap-authors" ><?php wp_list_authors('show_fullname=1&optioncount=1&exclude_admin=0'); ?></ul>
				</div> <!-- end .sitemap-col -->
			</div> <!-- end #sitemap -->

			<div class="clear"></div>

		</div> <!-- .entry-content -->
	</article> <!-- .et-additional-content -->

<?php get_footer(); ?>