<?php
if(!class_exists('WPCBCategoryArchive'))
{

class WPCBCategoryArchive extends cfct_build_module
{
	public function __construct()
	{
		$this->pluginDir		= basename(dirname(__FILE__));
		$this->pluginPath		= WP_PLUGIN_DIR . '/' . $this->pluginDir;
		$this->pluginUrl 		= WP_PLUGIN_URL.'/'.$this->pluginDir;	
		
		$opts = array
		(
			'description' => 'Choose and display an yearly archive from specific category.',
			'icon' => $this->pluginUrl.'/icon.png'
		);
		parent::__construct('cfct-wp-cb-category-archive', 'Yearly Archive', $opts);

	}

	public function display($data)
	{
		global $wpdb, $wp_query;

		$years = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) as year FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = 'post' ORDER BY post_date DESC", ARRAY_A);

		if(!isset($wp_query->query_vars['page']) || !is_numeric($wp_query->query_vars['page']))
		{
			
			if(sizeOf($years>0))
			{
				$year=$years[0];
			}
			else
			{
				return '';
			}
		}
		else
		{
			$year=$wp_query->query_vars['page'];
		}

        if (is_array($year)) $year = array_shift($year);


		$title=isset($data[$this->get_field_id('WPCBCategoryArchive_title')])?$data[$this->get_field_id('WPCBCategoryArchive_title')]:'';
		$category=isset($data[$this->get_field_id('WPCBCategoryArchive_category')])?$data[$this->get_field_id('WPCBCategoryArchive_category')]:'';
		
		
		//$year=array();
		$html='';
		
		/*$args = array(
			'category'        => get_cat_ID($category),
			'orderby'         => 'post_date',
			'order'           => 'DESC',
			'post_type'       => 'post',
			'post_status'     => 'publish' );
		$posts = get_posts($args);*/
		
		$posts=query_posts('year='.$year.'&post_status=publish&posts_per_page=-1&post_type=post&orderby=date&order=desc&category_name='.$category);

		$html .= '<div id="WPCBCategoryArchive_category">';
		foreach($posts as $ii=>$post)
		{
            $cls = 'line';
            if ($ii == 0) {
                $cls = 'first';
            }
			//$years[substr($post->post_date, 0, 4)] = 'subs';
            $html .= '<p class="'.$cls.'"><span class="date">'.date('d/m/Y', strtotime($post->post_date)).'</span>&nbsp;<a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a>&nbsp;'.apply_filters('get_the_excerpt', $post->post_excerpt).'&nbsp;<a class="plus" href="'.get_permalink($post->ID).'">+</a></p>';
		}

		$html .= '
		</div>';

        $vl = (strlen($s) ? $s : strtolower(__('Keyword Search')));
	
		$htmltmp = '<p><font class="headline">'.$title.'</font>';
        $htmltmp .= "<span class='rsslink'><a href='".get_option('siteurl')."?cat=".get_cat_ID($category)."&feed=rss2' style='float:right'>&nbsp;".__('RSS Feeds')."</a><a href='".get_option('siteurl')."?cat=".get_cat_ID($category)."&feed=rss2' style='float:right;text-decoration:none'><img src='".$this->pluginUrl.'/icon_rss.gif'."'/></a></span></p>";
		$htmltmp .= '
		<p>
		<form method="get" id="searchform" action="'.get_option('siteurl').'/">
			<div>
				<input type="text" class="text" value="'.wp_specialchars($vl, 1).'" name="s" id="s" />
				<input type="hidden" name="cat" value="'.get_cat_ID($category).'" />
				<input type="submit" id="searchsubmit" value="'.__('Search').'" />
			</div>
		</form>';
        $htmltmp .= '<p class="years">';
		foreach($years as $tyear)
		{
			$htmltmp .= '<a href="'.get_permalink().'/'.$tyear['year'].'">'.$tyear['year'].'</a> ';
		}
        $htmltmp .= '</p>';

		$htmltmp .= "</p>";
		
		
		
		echo $htmltmp.$html;

		wp_reset_query();
	}
	
	public function text($data)
	{
		return "Category: ".$data[$this->get_field_id('WPCBCategoryArchive_category')];
	}
	
	public function admin_form($data)
	{
		//(isset($data[$this->get_field_name('content')]) ? htmlspecialchars($data[$this->get_field_name('content')]) : null)
		
		$title=isset($data[$this->get_field_id('WPCBCategoryArchive_title')])?$data[$this->get_field_id('WPCBCategoryArchive_title')]:'';
		$category=isset($data[$this->get_field_id('WPCBCategoryArchive_category')])?$data[$this->get_field_id('WPCBCategoryArchive_category')]:'';
		
		$output =
		'	
		<table>
				<tr>
					<td><label for="WPCBCategoryArchive_title">Title</label></td>
					<td><input type="text" id="'.$this->get_field_id('WPCBCategoryArchive_title').'" name="'.$this->get_field_name('WPCBCategoryArchive_title').'" value="'.$title.'"/></td>
				</tr>
				
				<tr>
					<td><label for="WPCBCategoryArchive_category">Category</label>&nbsp;&nbsp;&nbsp;&nbsp;</td>
					<td>
						<select id="WPCBCategoryArchive_category" name="'.$this->get_field_name('WPCBCategoryArchive_category').'">
							<option></option>
							';
		$category_ids = get_all_category_ids();
		foreach($category_ids as $cat_id)
		{
			$cat_name = get_cat_name($cat_id);
			$output .= "<option ".(($category==$cat_name)?'selected ':'')."value='{$cat_name}'>".$cat_name."</option>";
		}							
							
		$output .=
							'.
						</select>
					</td>
				</tr>
			</table>
		';
		return $output;
	}
}

cfct_build_register_module('cfct-wp-cb-category-archive', 'WPCBCategoryArchive');
}