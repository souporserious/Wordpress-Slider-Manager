<?php
/*
Plugin Name: OE Slider Manager
Plugin URI: http://owleyes.co
Description: A slider manager to be used with any slider requiring an unordered list. Use the shortcode [oeSlider] to display images in a list array.
Author: Travis Arnold
Version: 2.1
*/
	
class OE_Slider {

	function __construct() {
		
		add_action('admin_menu', array( $this, 'register_menu_page' ) );
		
		add_shortcode( 'oeSlider', array( $this, 'shortcode' ) );
		
	}

	function register_menu_page() {
		
		$this->page_hook = add_menu_page( 'Slider', 'Slider', 'edit_theme_options', 'oeSlider_settings', array( $this, 'render_options_page' ), '', 22 );
		
		// make sure we only load assets on their respective page
		add_action('load-'.$this->page_hook, array($this, 'load_assets'));
		
	}
	
	function load_assets() {
		
		wp_enqueue_media();
	    
	    wp_enqueue_script('jquery');
	    wp_enqueue_script('jquery-ui-core');
	    wp_enqueue_script('jquery-ui-widget');
	    wp_enqueue_script('jquery-ui-mouse');
	    wp_enqueue_script('jquery-ui-draggable');
	    wp_enqueue_script('jquery-ui-droppable');
	    wp_enqueue_script('jquery-ui-sortable');
	    wp_enqueue_script('oeslider', plugins_url('oeSlider.js', __FILE__), array('jquery'), '', true);
		
		wp_enqueue_style('oeslider', plugins_url('oeSlider.css', __FILE__));
		
	}
	
	function render_options_page() {
		
		// Check If Page Is Loading After Form Submit Or Just Normally
		if( isset($_POST['slider_nonce']) && wp_verify_nonce($_POST['slider_nonce'],'slider-nonce') ) {  
		
		    $slides = $_POST['slide'];
		    update_option('oe_slides', $slides);
		    $count = count($slides) - 1;
		    
		} else {
		
		    $slides = get_option('oe_slides');
		    $count = (count($slides) == 0) ? 0 : count($slides) - 1;
		    
		}
	?>
	<script type="text/template" id="slide-template">
		<li class="slide">
			
			<input class="order" type="hidden" name="slide[%id%][order]" value="%id1%">
			
			<div class="slider group">
			
				<input type="hidden" name="slide[%id%][id]" class="image_id" value="">
				
				<div class="upload_preview">
					<div class="add-image">
						<?php echo '<img src="' . plugins_url( 'images/clickhere.jpg' , __FILE__ ) . '" alt="click here" />'; ?>
					</div>
					
					<div class="delete-slide">
						<a class="submitdelete deletion" href="#">Remove Slide</a>
					</div>
				</div>
				
				<div class="fields">
					<div>
						<label for="slide[%id%][title]">Title:</label>
						<input type="text" name="slide[%id%][title]" value="" placeholder="Title Here">
					</div>
					
					<div>	
						<label for="slide[%id%][sub_title]">Caption:</label>
						<input class="add_sub_title" type="text" name="slide[%id%][sub_title]" value="" placeholder="Sub Title Here">
					</div>
					
					<div>
						<label for="slide[%id%][link]">Link:</label>
						<input class="add_link" type="text" name="slide[%id%][link]" value="" placeholder="Copy &#38; Paste Link Here">
					</div>
				</div>
				
			</div>
			
			<div class="handle"></div>
		
		</li>
	</script>	
	
	<div id="oe-slider-admin" class="wrap">
	
		<h2>Slider Manager <a href="#" class="add-new-h2 add-slide">Add Slide</a></h2>
		
		<form name="oe-slider-form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		
			<?php sort($slides); ?>
			
			<ul id="manager_form_wrap" class="ui-sortable">
			<?php for ( $i=0; $i<=$count; $i++ ) { ?>
				<li class="slide">
					
					<input class="order" type="hidden" name="slide[<?php echo $i; ?>][order]" value="<?php echo $slides[$i]['order'] ? $slides[$i]['order'] : $i + 1; ?>">
					
					<div class="slider group">					
						
						<input type="hidden" name="slide[<?php echo $i; ?>][id]" class="image_id" value="<?php echo $slides[$i]['id']; ?>">
						
						<div class="upload_preview">
							<div class="add-image">
							<?php
							if( !$slides[$i]['id'] ){
								echo '<img src="' . plugins_url( 'images/clickhere.jpg' , __FILE__ ) . '" alt="click here" />';
							} else {
								$imgsrc = wp_get_attachment_image_src( $slides[$i]['id'], 'full' );
								echo '<img src="' . $imgsrc[0] . '" alt="click here" />';	
							}
							?>
							</div>
							
							<div class="delete-slide">
								<a class="submitdelete deletion" href="#">Remove Slide</a>
							</div>
						</div>
						
						<div class="fields">
							<div>
								<label for="slide[<?php echo $i; ?>][title]">Title:</label>
								<input type="text" name="slide[<?php echo $i; ?>][title]" value="<?php echo stripslashes($slides[$i]['title']) ?>" placeholder="Title Here">
							</div>
							
							<div>	
								<label for="slide[<?php echo $i; ?>][sub_title]">Caption:</label>
								<input type="text" name="slide[<?php echo $i; ?>][sub_title]" value="<?php echo stripslashes($slides[$i]['sub_title']) ?>" placeholder="Sub Title Here">
							</div>
							
							<div>
								<label for="slide[<?php echo $i; ?>][link]">Link:</label>
								<input type="text" name="slide[<?php echo $i; ?>][link]" value="<?php echo $slides[$i]['link']; ?>" placeholder="Copy &#38; Paste Link Here">
							</div>
						</div>
						
					</div>
					
					<div class="handle"></div>
				
				</li>
			<?php } ?>   
			</ul>
			
			<p class="submit">
				<?php wp_nonce_field('slider-nonce','slider_nonce', false); ?>
				<input type="submit" name="save-background-options" id="save-background-options" class="button button-primary" value="Save Changes">
			</p>
		
		</form>
	</div>
	<?php 
	
	echo '<pre>';
	print_r(get_option('oe_slides'));
	echo '</pre>';
	}
	
	function shortcode(){
		
		$slides = get_option('oe_slides');
	    $n = 1;
	
	    foreach( $slides as $slide ) { 
	    
	    	$link 	= ( empty($slide['link']) ) ? '#' : $slide['link'];
			$imgsrc = wp_get_attachment_image_src( $slide['id'], 'full' );
			?>
		    <li>
				<img src=<?php echo $imgsrc[0]; ?> alt="slide <?php echo $n; ?>"/>
				<div class="orbit-caption">
					<a href="<?php echo $link; ?>">
						<h1 class="heading"><?php echo stripslashes($slide['title']); ?></h1>
						<br>
						<p class="sub-heading"><?php echo stripslashes($slide['sub_title']); ?></p>
					</a>
				</div>
			</li>
		    <?php
		    $n++;
		}
	}
}

$GLOBALS['OE_Slider'] = new OE_Slider();