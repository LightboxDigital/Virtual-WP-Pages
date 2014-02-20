<?php
/*
    The concept is based upon a post at http://davejesch.com/wordpress/wordpress-tech/creating-virtual-pages-in-wordpress/

    We have done our best to make this as quick and intuitive to use as possible.
 */

if (!class_exists('LWDVirtualPages'))
{
    class LWDVirtualPages
    {
        private $slug = NULL;
        private $title = NULL;
        private $content = NULL;
        private $author = NULL;
        private $date = NULL;
        private $type = NULL;
        private $template = NULL;
 
        private function __construct($args)
        {
            if (!isset($args['slug']))
                throw new Exception('No slug given for virtual page');
 
            $this->slug = $args['slug'];
            $this->title = isset($args['title']) ? $args['title'] : '';
            $this->content = isset($args['content']) ? $args['content'] : '';
            $this->author = isset($args['author']) ? $args['author'] : 1;
            $this->date = isset($args['date']) ? $args['date'] : current_time('mysql');
            $this->dategmt = isset($args['date']) ? $args['date'] : current_time('mysql', 1);
            $this->type = isset($args['post_type']) ? $args['post_type'] : 'page';
            $this->template = isset($args['template']) ? $args['template'] : NULL;
 
            add_action('init', array(&$this, 'preparePage'));

        }
 
        public function preparePage()
        {
            // GET THE CURRENT PAGE REQUEST
            $url = $this->getRequest();

            // IF REQUEST MATCHES PAGES SLUG THEN BEGIN TO LOAD
            // THE FALSE PAGE

            if( $this->slug == $this->getRequest() )
            {
                // THROW THE NEW VIRTUAL PAGE INTO WORDPRESS USING
                // THE_POSTS FILTER
                add_filter( 'the_posts', array(&$this, 'virtualPage') );

                // IF THE TEMPLATE ARGUMENT IS SPECIFIED THEN FILTER
                // NEW TEMPLATE IN
                if( $this->template )
                    add_filter( 'template_include', array(&$this, 'loadTemplate'), 99 );
            }
        }

        public function virtualPage( $posts )
        {
            global $wp, $wp_query;

            // PREVENT CONFLICTS, MAKES FAUX PAGE LOW PRIORITY
 
            if (count($posts) == 0 &&
                (strcasecmp($wp->request, $this->slug) == 0 || $wp->query_vars['page_id'] == $this->slug))
            {
                // BEGIN TO CREATE FALSE POST
                $post = new stdClass;

                // FILL OUT POST OBJECT WITH VALUES
                $post->ID = -1;
                $post->post_author = $this->author;
                $post->post_date = $this->date;
                $post->post_date_gmt = $this->dategmt;
                $post->post_content = $this->content;
                $post->post_title = $this->title;
                $post->post_excerpt = '';
                $post->post_status = 'publish';
                $post->comment_status = 'closed';
                $post->ping_status = 'closed';
                $post->post_password = '';
                $post->post_name = $this->slug;
                $post->to_ping = '';
                $post->pinged = '';
                $post->modified = $post->post_date;
                $post->modified_gmt = $post->post_date_gmt;
                $post->post_content_filtered = '';
                $post->post_parent = 0;
                $post->guid = get_home_url('/' . $this->slug);
                $post->menu_order = 0;
                $post->post_type = $this->type;
                $post->post_mime_type = '';
                $post->comment_count = 0;
 
                // SET FILTERS RESULTS TO RETURN BACK INTO
                // THE_POSTS
                $posts = array( $post );
 
                // RESET QUERY TO ENSURE IT LOOKS LIKE A FOUND PAGE
                $wp_query->is_page = TRUE;
                $wp_query->is_singular = TRUE;
                $wp_query->is_home = FALSE;
                $wp_query->is_archive = FALSE;
                $wp_query->is_category = FALSE;
                unset($wp_query->query['error']);
                $wp_query->query_vars['error'] = '';
                $wp_query->is_404 = FALSE;
            }
 
            return ( $posts );
        }


        private function loadTemplate( $template )
        {
            // FILTERS TEMPLATE TO LOAD IF TEMPLATE ARGUMENT
            // IS SPECIFIED
            if( $overridden_template = locate_template( $this->template . '.php' ) )
            {
                // SET OVERRIDEN TEMPLATE TO BE RETURNED BY FILTER
                $template = $overridden_template;
            }

            return $template;
        }

        public function getRequest()
        {
            global $wp_rewrite;
            $request = '';
            // CHECK FOR PRETTY PERMALINKS OR NOT
            // THANKS TO http://wpandbacon.com/check-for-pretty-permalinks/
            if ($wp_rewrite->permalink_structure != '')
            {
                // THE REASON FOR USING THIS AS OPPOSE TO JUST $_SERVER
                // IS TO ENSURE IT WORKS FOR SITES THAT ARE RUNNING IN
                // SUBDIRECTORIES OF A SERVER

                // IT ALSO REQUIRES THAT THE SITES URL BE SET UP 
                // CORRECTLY IN WORDPRESS

                $url = $_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];

                $url = $this->stripProtocol( $url );

                $site = $this->stripProtocol( site_url() );

                $request = trim( preg_replace('/^' . preg_quote($site, '/') . '/', '', $url), '/' );
            }
            else
            {
                if( isset($_GET['vp'] && is_string($_GET['vp']))
                {
                    $request = $_GET['vp'];
                }
            }

            return $request;
        }

        private function stripProtocol( $url )
        {
            // REMOVE PROTOCOL FROM BEGINNING OF URL
            return preg_replace('#^[^:/.]*[:/]+#i', '', $url);
        }

    }
}

if( !function_exists('register_virtual_page') )
{
    function register_virtual_page( $args )
    {
        if( !is_array($args) || !isset( $args['slug']) )
            return false;
        else
            return new LWDVirtualPages( $args );
    }
}