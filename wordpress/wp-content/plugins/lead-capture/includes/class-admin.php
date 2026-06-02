<?php

defined( 'ABSPATH' ) || exit;

class Lead_Capture_Admin {

	public static function register(): void {
		add_action( 'admin_menu', array( self::class, 'add_menu' ) );
	}

	public static function add_menu(): void {
		add_menu_page(
			__( 'Lead Submissions', 'lead-capture' ),
			__( 'Lead Submissions', 'lead-capture' ),
			'manage_options',
			'lead-capture-submissions',
			array( self::class, 'render_page' ),
			'dashicons-email-alt',
			26
		);
	}

	public static function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'lead-capture' ) );
		}

		$per_page = 20;
		$page     = isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1;
		$offset   = ( $page - 1 ) * $per_page;
		$total    = Lead_Capture_Database::count();
		$rows     = Lead_Capture_Database::get_all( $per_page, $offset );
		$pages    = (int) ceil( $total / $per_page );

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Lead Submissions', 'lead-capture' ); ?></h1>
			<p><?php printf( esc_html__( 'Total submissions: %d', 'lead-capture' ), (int) $total ); ?></p>

			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'ID', 'lead-capture' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Name', 'lead-capture' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Email', 'lead-capture' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Phone', 'lead-capture' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Country', 'lead-capture' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Date of Birth', 'lead-capture' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Source', 'lead-capture' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Submitted', 'lead-capture' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $rows ) ) : ?>
						<tr>
							<td colspan="8"><?php esc_html_e( 'No submissions yet.', 'lead-capture' ); ?></td>
						</tr>
					<?php else : ?>
						<?php foreach ( $rows as $row ) : ?>
							<tr>
								<td><?php echo esc_html( (string) $row->id ); ?></td>
								<td><?php echo esc_html( $row->first_name . ' ' . $row->last_name ); ?></td>
								<td><a href="mailto:<?php echo esc_attr( $row->email ); ?>"><?php echo esc_html( $row->email ); ?></a></td>
								<td><?php echo esc_html( $row->phone ?: '—' ); ?></td>
								<td><?php echo esc_html( $row->country ?: '—' ); ?></td>
								<td><?php echo esc_html( $row->date_of_birth ?: '—' ); ?></td>
								<td><?php echo esc_html( $row->source ); ?></td>
								<td><?php echo esc_html( get_date_from_gmt( $row->created_at, 'Y-m-d H:i' ) ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>

			<?php if ( $pages > 1 ) : ?>
				<div class="tablenav bottom">
					<div class="tablenav-pages">
						<?php
						echo wp_kses_post(
							paginate_links(
								array(
									'base'      => add_query_arg( 'paged', '%#%' ),
									'format'    => '',
									'prev_text' => '&laquo;',
									'next_text' => '&raquo;',
									'total'     => $pages,
									'current'   => $page,
								)
							)
						);
						?>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
