<?
/*
* Plugin Name: EquityTools
* Description: Enabling the checklist based on Proposal of Inclusive Discrimination Act from National Assembly of the Republic of Korea
* Version: 0.2.2
* Author: Catswords Research
* Author URI: https://catswords.com
*/

class EquityTools {
    private static $types = array(
        "Gender",
        "Disability",
        "Age",
        "Language",
        "Country of origin",
        "Ethnicity",
        "Nationality",
        "Skin color",
        "Region",
        "Appearance",
        "Marital status",
        "Pregnancy or childbirth",
        "Family and household",
        "Religious",
        "Ideological or political opinions",
        "Criminal record",
        "Sexual orientation",
        "Gender identity",
        "Educational background",
        "Employment type",
        "Medical history or health status",
        "Social status"
    );

    public static function onCreate() {
        add_meta_box(
            'et_meta_box',          // this is HTML id of the box on edit screen
            'EquityTools',    // title of the box
            'EquityTools::onDraw',   // function to be called to display the checkboxes, see the function below
            'post',        // on which edit screen the box should appear
            'normal',      // part of page where the box should appear
            'default'      // priority of the box
        );
    }
    
    public static function onDraw() {
        // nonce field for security check, you can have the same
        // nonce field for all your meta boxes of same plugin
        wp_nonce_field( plugin_basename( __FILE__ ), 'et_nonce' );
        
        $post_id = get_the_ID();
        $_meta_et_types = get_post_meta( $post_id, 'et_types' );

        $et_types = array();
        if ($post_id) {
            $et_types = unserialize($_meta_et_types[0]);
        }

        echo '<p>Check all the topics that this content contains.</p>';
        echo '<ul>';
        foreach(self::$types as $type) {
            echo '<li><label><input type="checkbox" name="et_types[]" value="' . $type . '"';
            if (in_array($type, $et_types)) {
                echo ' checked="checked"';
            }
            echo '/> ' .$type . '</label></li>';
        }
        echo '</ul>';
        echo '</p>This checklist is based on <a href="https://www.lawmaking.go.kr/mob/nsmLmSts/out/2101116/detailR">Proposal of Inclusive Discrimination Act</a> from <a href="https://korea.assembly.go.kr:447/">National Assembly of the Republic of Korea</a></p>';
    }
    
    public static function onRequest($post_id) {
        // check if this isn't an auto save
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return;

        // security check
        if ( !wp_verify_nonce( $_POST['et_nonce'], plugin_basename( __FILE__ ) ) ) // spelling fix
            return;

        // further checks if you like, 
        // for example particular user, role or maybe post type in case of custom post types

        // now store data in custom fields based on checkboxes selected
        $et_types = $_POST['et_types'];

        if ($et_types) {
            update_post_meta( $post_id, 'et_types', serialize($et_types) );
        } else {
            update_post_meta( $post_id, 'et_types', '' );
        }
    }

    public static function afterContent($content) {
        $_contents = '';

        $post_id = get_the_ID();
        $_meta_et_types = get_post_meta( $post_id, 'et_types' );

        $et_types = array();
        if ($post_id) {
            $et_types = unserialize($_meta_et_types[0]);
        }

        if ($et_types !== false && count($et_types) > 0) {
            $_contents .= '<p style="color: #767676; font-weight: bold;">This content includes topics related to: ';
            $_contents .= implode(', ', $et_types);
            $_contents .= '</p>';
        }

        $_contents .= $content;

        return $_contents;
    }
}

add_action( 'add_meta_boxes', 'EquityTools::onCreate' );
add_action( 'save_post', 'EquityTools::onRequest' );
add_filter('the_content', 'EquityTools::afterContent');
