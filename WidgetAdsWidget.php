<?php
// This file may not be executed directly
if (!defined('ABSPATH')) {
  exit();
}

class WidgetAdsWidget extends WP_Widget
{

  public function __construct()
  {
    parent::__construct(
        'widget_ads',
        'Widget Ads'
    );
  }

  public function widget($args, $instance)
  {
    echo "<aside class=\"widget\">";
    echo WidgetAdsGUI::display_widget($instance);
    echo "</aside>";
  }

  public function form($instance)
  {
    $allWidgetAds = WidgetAd::get_all();
    $selected = (isset($instance['selectedad'])) ? $instance['selectedad']: '';
    ?>
    <p>
      <strong>Select a Widget Ad</strong>
    </p>
    <div class="wads-widget-list">
      <?php foreach($allWidgetAds as $widget): ?>
        <div class="widget-row">
          <input type="radio" value="<?php echo $widget->getID(); ?>" id="<?php echo $this->get_field_id('selectedad'); ?>" name="<?php echo $this->get_field_name('selectedad'); ?>" <?php echo ($selected == $widget->getID()) ? 'checked': ''; ?>/>
          <?php if ($widget->hasImage()): ?>
            <img src="<?php echo esc_url($widget->getImageURL()); ?>" />
          <?php else: ?>
            <strong>
              <?php
              $topText = $widget->getTopText();
              echo (!empty($topText)) ? $topText: $widget->getBottomText();
              ?>
            </strong>
          <?php endif; ?>
          <br/>
          <em><?php echo $widget->getLink(); ?></em>
        </div>
      <?php endforeach; ?>
      <?php if (count($allWidgetAds) < 1): ?>
        <div class="widget-row">
          <em>There are currently no Widget Ads</em>
        </div>
      <?php endif; ?>
    </div>
    <?php
  }

  public function update($new_instance, $old_instance)
  {
    $instance = array();
    $instance['selectedad'] = (isset($new_instance['selectedad'])) ? $new_instance['selectedad']: '';
    return $instance;
  }

}
