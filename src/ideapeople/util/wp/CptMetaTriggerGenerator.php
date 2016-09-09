<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-29
 * Time: 오후 1:19
 */

namespace ideapeople\util\wp;


class CptMetaTriggerGenerator {
	public $trigger_table_name;
	public $trigger_names = array(
		'i',
		'u',
		'd'
	);
	public $post_type;

	public function __construct( $trigger_table_name, $post_type ) {
		global $wpdb;

		$this->trigger_table_name = "{$wpdb->prefix}$trigger_table_name";
		$this->post_type          = $post_type;
	}

	public function execute() {
		$this->create_meta_trigger_table();

		$this->create_meta_triggers();
	}

	public function create_meta_trigger_table() {
		global $wpdb;

		if ( ! $this->existsTable( $this->trigger_table_name ) ) {
			$sql = "CREATE TABLE {$this->trigger_table_name} LIKE {$wpdb->postmeta};";
			$wpdb->query( $sql );
		}
	}

	public function create_meta_triggers() {
		global $wpdb;

		$insert_trigger = "CREATE OR REPLACE TRIGGER {$this->trigger_table_name}_i AFTER INSERT ON {$wpdb->postmeta} FOR EACH ROW
							BEGIN
							 declare p_post_type VARCHAR(50) default '';
							 SELECT post_type INTO p_post_type FROM {$wpdb->posts} WHERE ID=NEW.post_id;
								IF p_post_type = '{$this->post_type}' then
								INSERT INTO {$this->trigger_table_name} VALUES
									(
										NEW.meta_id,
										NEW.post_id,
										NEW.meta_key,
										NEW.meta_value
									) ;
								end if;
							END;";

		$wpdb->query( $insert_trigger );

		$update_trigger = "delimiter #
							CREATE
							OR REPLACE TRIGGER {$this->trigger_table_name}_u AFTER UPDATE ON {$wpdb->postmeta} FOR EACH ROW
							BEGIN
							 declare p_post_type VARCHAR(50) default '';
							 declare p_has_key BIGINT(20) default 0;
							 SELECT post_type INTO p_post_type FROM {$wpdb->posts} WHERE ID=NEW.post_id;
							 SELECT count(meta_value) INTO p_has_key FROM {$this->trigger_table_name} WHERE meta_key=NEW.meta_key;
								IF p_post_type = '{$this->post_type}' then
									IF p_has_key > 0 THEN
										UPDATE {$this->trigger_table_name}
											SET
												meta_value = NEW.meta_value
											WHERE meta_id=NEW.meta_id;
									ELSE
										INSERT INTO {$this->trigger_table_name}
										VALUES (
												NEW.meta_id,
												NEW.post_id,
												NEW.meta_key,
												NEW.meta_value
											);
									END IF;
								END if;
							END #
							delimiter ;";

		$wpdb->query( $update_trigger );

		$delete_trigger = "delimiter #
							CREATE
							OR REPLACE TRIGGER {$this->trigger_table_name}_d AFTER DELETE ON {$wpdb->postmeta} FOR EACH ROW
							BEGIN
							 declare p_post_type VARCHAR(50) default '';
							 SELECT post_type INTO p_post_type FROM {$wpdb->posts} WHERE ID=OLD.post_id;
								IF p_post_type = '{$this->post_type}' then
									DELETE FROM {$this->trigger_table_name} WHERE meta_id=OLD.meta_id;
								end if;
							END #
							delimiter ;";

		$wpdb->query( $delete_trigger );
	}

	public function enable_trigger() {
		global $wpdb;

		foreach ( $this->trigger_names as $name ) {
			$sql = "ALTER TRIGGER {$this->trigger_table_name}_{$name} ENABLE";
			$wpdb->query( $sql );
		}
	}

	public function disable_trigger() {
		global $wpdb;

		foreach ( $this->trigger_names as $name ) {
			$sql = "ALTER TRIGGER {$this->trigger_table_name}_{$name} DISABLE";
			$wpdb->query( $sql );
		}
	}

	public function existsTable( $tableName ) {
		global $wpdb;

		$query = $wpdb->prepare( "
            SELECT COUNT(*) AS count
            FROM information_schema.tables
            WHERE table_schema = '" . DB_NAME . "'
            AND table_name = %s ", $tableName );

		return $wpdb->get_var( $query );
	}
}