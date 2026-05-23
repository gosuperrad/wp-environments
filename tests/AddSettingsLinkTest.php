<?php
/**
 * Tests for add_settings_link().
 *
 * @package SuperRad\WP_Environments
 */

namespace SuperRad\WP_Environments\Tests;

use PHPUnit\Framework\TestCase;
use function SuperRad\WP_Environments\add_settings_link;

final class AddSettingsLinkTest extends TestCase {

	public function test_prepends_settings_link_to_existing_links(): void {
		$existing = array( '<a href="#deactivate">Deactivate</a>' );

		$result = add_settings_link( $existing );

		$this->assertCount( 2, $result );
		$this->assertStringContainsString( 'page=wpe_settings', $result[0] );
		$this->assertStringContainsString( 'Settings', $result[0] );
		$this->assertSame( $existing[0], $result[1] );
	}

	public function test_returns_only_the_settings_link_when_none_exist(): void {
		$result = add_settings_link( array() );

		$this->assertCount( 1, $result );
		$this->assertStringContainsString( 'page=wpe_settings', $result[0] );
	}
}
