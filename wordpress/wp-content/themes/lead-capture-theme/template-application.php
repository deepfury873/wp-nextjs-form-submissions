<?php
/**
 * Template Name: Application Form
 *
 * @package Lead_Capture_Theme
 */

defined( 'ABSPATH' ) || exit;

$countries = array(
	''              => __( 'Choose Country', 'lead-capture-theme' ),
	'United States' => __( 'United States', 'lead-capture-theme' ),
	'United Kingdom'=> __( 'United Kingdom', 'lead-capture-theme' ),
	'Canada'        => __( 'Canada', 'lead-capture-theme' ),
	'Australia'     => __( 'Australia', 'lead-capture-theme' ),
	'Germany'       => __( 'Germany', 'lead-capture-theme' ),
	'France'        => __( 'France', 'lead-capture-theme' ),
	'India'         => __( 'India', 'lead-capture-theme' ),
	'Israel'        => __( 'Israel', 'lead-capture-theme' ),
	'Other'         => __( 'Other', 'lead-capture-theme' ),
);

$theme_uri = get_template_directory_uri();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'lead-capture-page' ); ?>>
<?php wp_body_open(); ?>

<main class="lc-page" id="main-content">
	<p class="lc-page__eyebrow"><?php esc_html_e( 'Submit Your Application', 'lead-capture-theme' ); ?></p>

	<section class="lc-card" aria-labelledby="lc-form-heading">
		<header class="lc-card__header">
			<h1 id="lc-form-heading" class="lc-card__title"><?php esc_html_e( 'Personal Information', 'lead-capture-theme' ); ?></h1>
			<p class="lc-card__subtitle"><?php esc_html_e( 'Please fill in all mandatory fields', 'lead-capture-theme' ); ?></p>
		</header>

		<form
			id="lead-capture-form"
			class="lc-form"
			novalidate
			aria-describedby="lc-form-status"
		>
			<div class="lc-form__grid">
				<div class="lc-field">
					<label class="lc-field__label lc-sr-only" for="first_name"><?php esc_html_e( 'First Name', 'lead-capture-theme' ); ?></label>
					<input
						class="lc-field__input"
						type="text"
						id="first_name"
						name="first_name"
						placeholder="<?php esc_attr_e( '*First Name', 'lead-capture-theme' ); ?>"
						autocomplete="given-name"
						required
						aria-required="true"
						maxlength="100"
					>
					<p class="lc-field__error" id="first_name-error" role="alert" hidden></p>
				</div>

				<div class="lc-field">
					<label class="lc-field__label lc-sr-only" for="last_name"><?php esc_html_e( 'Last Name', 'lead-capture-theme' ); ?></label>
					<input
						class="lc-field__input"
						type="text"
						id="last_name"
						name="last_name"
						placeholder="<?php esc_attr_e( '*Last Name', 'lead-capture-theme' ); ?>"
						autocomplete="family-name"
						required
						aria-required="true"
						maxlength="100"
					>
					<p class="lc-field__error" id="last_name-error" role="alert" hidden></p>
				</div>

				<div class="lc-field">
					<label class="lc-field__label lc-sr-only" for="email"><?php esc_html_e( 'Email', 'lead-capture-theme' ); ?></label>
					<input
						class="lc-field__input"
						type="email"
						id="email"
						name="email"
						placeholder="<?php esc_attr_e( '*Email', 'lead-capture-theme' ); ?>"
						autocomplete="email"
						required
						aria-required="true"
					>
					<p class="lc-field__error" id="email-error" role="alert" hidden></p>
				</div>

				<div class="lc-field">
					<label class="lc-field__label lc-sr-only" for="phone"><?php esc_html_e( 'Phone Number', 'lead-capture-theme' ); ?></label>
					<input
						class="lc-field__input"
						type="tel"
						id="phone"
						name="phone"
						placeholder="<?php esc_attr_e( 'Phone Number', 'lead-capture-theme' ); ?>"
						autocomplete="tel"
						inputmode="tel"
					>
					<p class="lc-field__error" id="phone-error" role="alert" hidden></p>
				</div>

				<div class="lc-field">
					<label class="lc-field__label lc-sr-only" for="country"><?php esc_html_e( 'Country', 'lead-capture-theme' ); ?></label>
					<div class="lc-field__select-wrap">
						<select class="lc-field__input lc-field__input--select" id="country" name="country">
							<?php foreach ( $countries as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="lc-field">
					<label class="lc-field__label lc-sr-only" for="date_of_birth"><?php esc_html_e( 'Date of Birth', 'lead-capture-theme' ); ?></label>
					<div class="lc-field__date-wrap lc-field__date-wrap--empty" id="date_of_birth-wrap">
						<input
							class="lc-field__input"
							type="date"
							id="date_of_birth"
							name="date_of_birth"
						>
					</div>
					<p class="lc-field__error" id="date_of_birth-error" role="alert" hidden></p>
				</div>
			</div>

			<hr class="lc-form__divider" aria-hidden="true">

			<div class="lc-form__consent">
				<input
					class="lc-checkbox"
					type="checkbox"
					id="consent"
					name="consent"
					required
					aria-required="true"
				>
				<label class="lc-checkbox__label" for="consent">
					<?php esc_html_e( 'I have read and agree to the', 'lead-capture-theme' ); ?>
					<a href="#" class="lc-link"><?php esc_html_e( 'Terms and Conditions', 'lead-capture-theme' ); ?></a>
					<?php esc_html_e( 'and the', 'lead-capture-theme' ); ?>
					<a href="#" class="lc-link"><?php esc_html_e( 'Privacy Policy', 'lead-capture-theme' ); ?></a>
				</label>
			</div>
			<p class="lc-field__error lc-field__error--consent" id="consent-error" role="alert" hidden></p>

			<div class="lc-form__actions">
				<button type="submit" class="lc-button" id="submit-btn">
					<?php esc_html_e( 'SUBMIT >', 'lead-capture-theme' ); ?>
				</button>
			</div>

			<p id="lc-form-status" class="lc-form__status" role="status" aria-live="polite"></p>

			<div class="lc-card__illustration" aria-hidden="true">
				<img
					src="<?php echo esc_url( $theme_uri . '/assets/images/form-illustration.png' ); ?>"
					width="301"
					height="280"
					alt=""
					class="lc-card__illustration-img lc-card__illustration-img--desktop"
				>
				<img
					src="<?php echo esc_url( $theme_uri . '/assets/images/form-illustration-mobile.png' ); ?>"
					width="216"
					height="196"
					alt=""
					class="lc-card__illustration-img lc-card__illustration-img--mobile"
				>
			</div>
		</form>
	</section>
</main>

<?php wp_footer(); ?>
</body>
</html>
