<?php
/**
 * Fallback index template.
 *
 * @package Lead_Capture_Theme
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php bloginfo( 'name' ); ?></title>
	<?php wp_head(); ?>
</head>
<body>
	<main style="padding:2rem;font-family:sans-serif;max-width:40rem;margin:auto;">
		<h1><?php bloginfo( 'name' ); ?></h1>
		<p><?php esc_html_e( 'Create a page and assign the "Application Form" template to display the lead capture form.', 'lead-capture-theme' ); ?></p>
	</main>
	<?php wp_footer(); ?>
</body>
</html>
