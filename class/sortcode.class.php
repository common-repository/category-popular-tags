<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

//credit : https://wordpress.stackexchange.com/questions/261617/display-most-popular-tags-of-category

class Cush_CPT_Shortcode
{
    public $verbose;
    public $type;
    public $count;
    public $category_name;
    public $category_id;
    public $category_transient;
    public $term_transient;
    public $content;



    public function __construct()
    {
        add_shortcode('popular_category_tags', array($this, 'tags_shortcode'));
    }


    public function tags_shortcode($attr, $content = null)
    {
        extract(shortcode_atts(array(
            'type' => '',
            'count' => '',
            'verbose' => '0',    // '1' outputs some debug info
            'category_name' => '',
            'category_id' => ''
        ), $attr));

        $options = get_option('pct_option');                   
        $tags_total = !empty($options['total_tag']) ? $options['total_tag'] : 5;

        $this->verbose = ($this->verbose !== '0');
        $this->type = $attr['type'];
        $this->count = isset($attr['count']) ? $attr['count'] : $tags_total;
        $this->category_name =  !isset($attr['category_name']) || $attr['category_name'] == ''; //$attr['category_name'];
        $this->category_id = $attr['category_id'];

        if (!empty($this->type) && !empty($this->count)) :

            $current_cat_ids = array();

            global $post;

            if(empty($this->category_id)) {
                $postcat = get_the_category( $post->ID ); //set category id incase no category provided
                if ( ! empty( $postcat ) ) {
                    $this->category_id = $postcat[0]->cat_ID;   
                }
            }


            if (!empty($post)) {
                // get all categories of current post
                $terms = get_the_terms($post->ID, 'category');
                if ($terms && !is_wp_error($terms)) {
                    foreach ($terms as $term) {
                        $current_cat_ids[] = $term->term_id;
                    }
                }
            } else {
                if ($this->verbose) $this->content = 'tags_from_category: no $post...';
            }

            if (!empty($current_cat_ids)) {
                // get all post ids of current categories ordered by date
                $args = array(
                    'posts_per_page' => -1,
                    'orderby' => 'post_date',
                    'order' => 'DESC',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'category',
                            'field' => 'id',
                            'terms' => $current_cat_ids
                        )
                    ),
                    'suppress_filters' => false,
                    'fields' => 'ids'
                );
               
                if (!empty($this->category_id)) {
                    $args['category_id']  = $attr['category_id'];
                }
                if ("0" == $this->category_id) {
                    $categories = get_the_category();
                    if (!empty($categories)) {
                        $this->category_id = get_cat_ID($categories[0]->name);
                       
                    }
                }

                if ("" == $this->category_name) {
                    $cat_name = get_cat_name($this->category_id);
                        $categories = get_the_category();
                        if (!empty($categories)) {
                            $this->category_transient = sanitize_title_with_dashes($cat_name);
                            
                        }
                } else {
                    $this->category_transient = $this->category_name;
                }

               


                $popular_transient_name = 'cush_cpt_popular_' . $this->category_transient . '_' . $this->category_id;

                if (false === ($my_post_ids = get_transient($popular_transient_name))) {

                    $my_post_ids = get_posts($args);
                    set_transient($popular_transient_name, $my_post_ids, 360 * 7200);
                }

                if (!empty($my_post_ids)) {

                    switch ($this->type) {
                        case 'popular-tags':

                            $term_transient_name = 'cush_cpt_terms_' . $this->category_transient . '_' . $this->category_id;

                            // get terms orderby count
                            // note: wp_get_object_terms for post_id array returns total count of terms on all categories!
                            if (false === ($found_terms = get_transient($term_transient_name))) {

                                $found_terms = array();
                                // get terms & count for all post ids of current categories
                                foreach ($my_post_ids as $my_post_id) {
                                    $terms = get_the_terms($my_post_id, 'post_tag');
                                    if (!is_wp_error($terms) && !empty($terms)) {
                                        foreach ($terms as $term) {
                                            if (array_key_exists('id_' . $term->term_id, $found_terms)) {
                                                $found_terms['id_' . $term->term_id]['count']++;
                                            } else {
                                                $found_terms['id_' . $term->term_id] = array(
                                                    'count' => 1,
                                                    'slug' => $term->slug,
                                                    'name' => $term->name
                                                );
                                            }
                                        }
                                    }
                                }
                                set_transient($term_transient_name, $found_terms, 360 * 7200);
                            }




                            if (!empty($found_terms)) {
                                // sort terms by count
                                $found_terms_by_count = array();
                                foreach ($found_terms as $term_id => $term) {
                                    $found_terms_by_count[$term_id] = $term['count'];
                                }
                                array_multisort($found_terms_by_count, SORT_DESC, $found_terms);
                                // get $count terms order by count
                                // loop through ordered terms until enough tags
                                $options = get_option('pct_option');
                                $pct_title = !empty($options['title']) ? $options['title'] : 'Popular Tags';
                                
                                $this->content = '<div class="cush-archive-section">';
                                //v.2 to do use option to check if user want to display title 
                                $this->content .= '<div class="cush-archive-section-title"><h2>';
                                $this->content .= $pct_title.'</h2></div>';
                                $this->content .= '<div class="cush-archive-tag-block cush-archive-block">';
                                $term_count = 0;
                                $current_num = 1;
                                foreach ($found_terms as $term) {
                                    $this->content .= '<a class="cush-archive-tag cush-archive-element" href="' . esc_url(get_term_link($term['slug'], 'post_tag')) . '" title="' . esc_attr($term['name']) . '">';
                                    if( "1" == $options['number_enable']) $this->content .='<span>'.$current_num.'</span>';
                                    $arrow = '';
                                    if( "1" == $options['arrow_enable']) 
                                    $arrow = '-arrow-right';
                                    $this->content .='<div class="pct-tag-name'.$arrow.'">' . $term['name'] . '</div></a>';
                                    $term_count++;
                                    $current_num++;
                                    if ($term_count >= $this->count) {
                                        break;
                                    }
                                }
                                $this->content .= '</div></div>';
                            } else {
                                if ($this->verbose) $this->content = 'tags_from_category: no tags attached to posts in current categories...';
                            }

                            break;
                        case 'last-tags':

                            // get $count terms orderby post_date
                            // loop through ordered posts until enough tags
                            $this->content = '';
                            $found_ids = array();
                            foreach ($my_post_ids as $my_post_id) {
                                $terms = get_the_terms($my_post_id, 'post_tag');
                                if (!is_wp_error($terms) && !empty($terms)) {
                                    foreach ($terms as $term) {
                                        if (!in_array($term->term_id, $found_ids)) {
                                            $this->content .= '<li><a href="' . esc_url(get_term_link($term->slug, 'post_tag')) . '" title="' . esc_attr($term->name) . '">' . $term->name . '</a></li>';
                                            $found_ids[] = $term->term_id;
                                            if (count($found_ids) >= $this->count) {
                                                break 2;
                                            }
                                        }
                                    }
                                }
                            }
                            if (!empty($this->content)) {
                                $this->content = '<ul>' . $this->content . '</ul>';
                            } else {
                                if ($this->verbose) $this->content = ''; // error msg here : no tags attached
                            }

                            break;
                        default:
                            if ($this->verbose) $this->content = 'tags_from_category: unknown "type"...';
                            break;
                    }
                } else {
                    if ($this->verbose) $this->content = 'tags_from_category: no posts in current categories...';
                }
            } else {
                if ($this->verbose) $this->content = ''; //error msg goes here: tags_from_category: no current categories...
            } else :
            if ($this->verbose) $this->content = 'tags_from_category: bad params "type" and/or "count"...';
        endif;

        return $this->content;
    }
}
