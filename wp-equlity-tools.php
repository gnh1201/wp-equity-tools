<?
/*
* Plugin Name: EqulityTools
* Description: Enabling the checklist based on Proposal of Inclusive Discrimination Act from National Assembly of the Republic of Korea
* Version: 0.1
* Author: Catswords Research
* Author URI: https://catswords.com
*/

class EqulityTools {
    private static $types = array(
        "Disability",
        "Age",
        "Language",
        "Country of origin",
        "Ethnicity",
        "Nationality",
        "Skin color",
        "Physical conditions",
        "Marital status",
        "Pregnancy or childbirth",
        "Family and household",
        "Religious",
        "Ideological or political opinions",
        "Criminal record",
        "Sexual orientation",
        "Gender identity",
        "Employment type",
        "Medical history or health status",
        "Social status"
    );

    public static function onCreate() {
        add_meta_box(
            'et_meta_box',          // this is HTML id of the box on edit screen
            'EqulityTools',    // title of the box
            'EqulityTools::onDraw',   // function to be called to display the checkboxes, see the function below
            'post',        // on which edit screen the box should appear
            'normal',      // part of page where the box should appear
            'default'      // priority of the box
        );
    }
    
    public static function onDraw() {
        // nonce field for security check, you can have the same
        // nonce field for all your meta boxes of same plugin
        wp_nonce_field( plugin_basename( __FILE__ ), 'et_nonce' );

        echo '<p>Check all the topics that this content contains.</p>';
        echo '<ul>';
        foreach(self::$types as $type) {
            echo '<li><label><input type="checkbox" name="et_types[]" value="' . $type . '" /> ' .$type . '</label></li>';
        }
        echo '</ul>';
        echo '</p>This checklist is based on <a href="https://www.lawmaking.go.kr/mob/nsmLmSts/out/2101116/detailR">Proposal of Inclusive Discrimination Act</a> from <a href="https://korea.assembly.go.kr:447/">National Assembly of the Republic of Korea</a></p>';
    }
    
    public static function onRequest() {
        // check if this isn't an auto save
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return;

        // security check
        if ( !wp_verify_nonce( $_POST['et_nonce'], plugin_basename( __FILE__ ) ) ) // spelling fix
            return;

        // further checks if you like, 
        // for example particular user, role or maybe post type in case of custom post types

        // now store data in custom fields based on checkboxes selected
        if ( isset( $_POST['et_types'] ) ) {
            update_post_meta( $post_id, 'et_types', serialize($et_types) );
        } else {
            update_post_meta( $post_id, 'et_types', '' );
        }
    }
}

add_action( 'add_meta_boxes', 'EqulityTools::onCreate' );
add_action( 'save_post', 'EqulityTools::onRequest' );