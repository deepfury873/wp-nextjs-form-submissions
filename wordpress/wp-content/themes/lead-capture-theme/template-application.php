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
	<p class="lc-page__eyebrow">Submit Your Application</p>

	<section class="lc-card" aria-labelledby="lc-form-heading">
		<header class="lc-card__header">
			<h1 id="lc-form-heading" class="lc-card__title">Personal Information</h1>
			<p class="lc-card__subtitle">Please fill in all mandatory fields</p>
		</header>

		<form
			id="lead-capture-form"
			class="lc-form"
			novalidate
			aria-describedby="lc-form-status"
		>
			<div class="lc-form__grid">
				<div class="lc-field">
					<label class="lc-field__label" for="first_name">
						<span class="lc-field__required" aria-hidden="true">*</span>First Name
					</label>
					<input
						class="lc-field__input"
						type="text"
						id="first_name"
						name="first_name"
						autocomplete="given-name"
						required
						aria-required="true"
						maxlength="100"
					>
					<p class="lc-field__error" id="first_name-error" role="alert" hidden></p>
				</div>

				<div class="lc-field">
					<label class="lc-field__label" for="last_name">
						<span class="lc-field__required" aria-hidden="true">*</span>Last Name
					</label>
					<input
						class="lc-field__input"
						type="text"
						id="last_name"
						name="last_name"
						autocomplete="family-name"
						required
						aria-required="true"
						maxlength="100"
					>
					<p class="lc-field__error" id="last_name-error" role="alert" hidden></p>
				</div>

				<div class="lc-field">
					<label class="lc-field__label" for="email">
						<span class="lc-field__required" aria-hidden="true">*</span>Email
					</label>
					<input
						class="lc-field__input"
						type="email"
						id="email"
						name="email"
						autocomplete="email"
						required
						aria-required="true"
					>
					<p class="lc-field__error" id="email-error" role="alert" hidden></p>
				</div>

				<div class="lc-field">
					<label class="lc-field__label" for="phone">Phone Number</label>
					<input
						class="lc-field__input"
						type="tel"
						id="phone"
						name="phone"
						autocomplete="tel"
						inputmode="tel"
					>
					<p class="lc-field__error" id="phone-error" role="alert" hidden></p>
				</div>

				<div class="lc-field">
					<label class="lc-field__label" for="country">Country</label>
					<div class="lc-field__select-wrap">
						<select class="lc-field__input lc-field__input--select" id="country" name="country">
							<?php foreach ( $countries as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>

				<div class="lc-field">
					<label class="lc-field__label" for="date_of_birth">Date of Birth</label>
					<div class="lc-field__date-wrap">
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
					I have read and agree to the
					<a href="#" class="lc-link">Terms and Conditions</a>
					and the
					<a href="#" class="lc-link">Privacy Policy</a>
				</label>
			</div>
			<p class="lc-field__error lc-field__error--consent" id="consent-error" role="alert" hidden></p>

			<div class="lc-form__actions">
				<button type="submit" class="lc-button" id="submit-btn">
					<span class="lc-button__text">Submit</span>
					<span class="lc-button__arrow" aria-hidden="true">&gt;</span>
				</button>
			</div>

			<p id="lc-form-status" class="lc-form__status" role="status" aria-live="polite"></p>

			<div class="lc-card__illustration" aria-hidden="true">
				<?php include get_template_directory() . '/assets/images/illustration.svg'; ?>
			</div>
		</form>
	</section>
</main>

<?php wp_footer(); ?>
</body>
</html>
