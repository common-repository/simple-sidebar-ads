<?php
// This file may not be executed directly
if (!defined('ABSPATH')) {
  exit();
}

class WidgetAdsGUI
{

  private static $notice = array();

  public static function display_menu_page($post)
  {
    $allWidgetAds = WidgetAd::get_all();
    ?>
    <div class="wrap">
      <h1>Widget Ads <a href="admin.php?page=wads_menu_add_new" class="page-title-action">Add New</a></h1>
      <br/>
      <form action="" method="post">
      <?php wp_nonce_field('widget_ads_list_nonce_field', 'widget_ads_list_nonce'); ?>
      <div class="tablenav top">
      	<div class="alignleft actions bulkactions">
          <select name="wads_action">
            <option value="-1" selected="selected">Bulk Actions</option>
      	    <option value="delete">Delete</option>
          </select>
          <input type="submit" class="button action" value="Apply">
      	</div>
      	<br class="clear"/>
    	</div>
      <table class="wp-list-table widefat fixed striped">
        <thead>
          <tr>
            <td class="manage-column column-cb check-column" style="width:30px;"><input type="checkbox"></td>
            <th>Image</th>
            <th>Link</th>
            <th>Top Text</th>
            <th>Overlay Text</th>
            <th>Bottom Text</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($allWidgetAds as $widgetAd): ?>
            <tr>
              <th scope="row" class="check-column"><label><input name="widgetads[]" value="<?php echo $widgetAd->getID(); ?>" type="checkbox"/></label></th>
              <?php if ($widgetAd->hasImage()): ?>
                <td><img src="<?php echo esc_url($widgetAd->getImageURL()); ?>" style="max-width:110px;max-height:110px;"/></td>
              <?php else: ?>
                <td>No Image</td>
              <?php endif; ?>
              <td><?php echo $widgetAd->getLink(); ?></td>
              <td><?php echo $widgetAd->getTopText(); ?></td>
              <td><?php echo $widgetAd->getOverlayText(); ?></td>
              <td><?php echo $widgetAd->getBottomText(); ?><a style="float:right;" class="button-secondary" href="<?php echo esc_url(admin_url('admin.php?page=wads_menu_add_new&adid=' . $widgetAd->getID())); ?>">Edit</a></td>
            </tr>
          <?php endforeach; ?>
          <?php if (count($allWidgetAds) < 1): ?>
            <tr>
              <td colspan="6"><em>There are currently no Widget Ads</em></td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
      </form>
    </div>
    <?php
  }

  public static function display_submenu_page()
  {
    $updating = false;
    if (isset($_GET['adid']) && !empty($_GET['adid'])) {
      $ad_id = intval($_GET['adid']);
      $widgetAd = new WidgetAd($ad_id);
      if ($widgetAd->getID() > 0) {
        $updating = true;
      }
    }
    ?>
    <div class="wrap">
      <h1>Add New Widget Ad</h1>

      <div class="add-new-content">
        <?php if ($updating): ?>
        <form action="<?php echo esc_url(admin_url('admin.php?page=wads_menu_add_new&adid=' . $widgetAd->getID())); ?>" method="post">
        <?php else: ?>
        <form action="<?php echo esc_url(admin_url('admin.php?page=wads_menu_add_new')); ?>" method="post">
        <?php endif; ?>
          <div style="overflow: auto;">
            <div id="wads-image" class="wads-image">
              <br/>
              <div id="wads-image-settings" class="wads-image-settings" <?php echo ($updating && $widgetAd->hasImage()) ? '': 'style="display:none;"'; ?>>
                Image Width:&nbsp; &nbsp;<input value="<?php echo ($updating) ? $widgetAd->getImageWidth(): ''; ?>" name="wads_image_width" id="wads-image-width" type="text" /><br/>
                Image Height: &nbsp;<input value="<?php echo ($updating) ? $widgetAd->getImageHeight(): ''; ?>" name="wads_image_height" id="wads-image-height" type="text" />
              </div>
              <div id="wads-placeholder-image" class="wads-placeholder-image" <?php echo ($updating && $widgetAd->hasImage()) ? 'style="background-image:url(\'' . $widgetAd->getImageURL() . '\');"': ''; ?>>&nbsp;</div>
              <br/>
              <div class="wads-under-image">
                <button id="wads-select-image" class="button-primary">Select Image</button>
                <span id="wads-clear-image" <?php echo ($updating && $widgetAd->hasImage()) ? '': 'style="display:none;"'; ?>>
                  &nbsp;<button id="wads-clear-image-button" class="button-secondary">Clear Image</button>
                </span>
                <input type="hidden" id="wads_attachment_id" name="wads_attachment_id" value="<?php echo ($updating && $widgetAd->hasImage()) ? $widgetAd->getImage(): ''; ?>"/>
                <?php
                wp_nonce_field('widget_ads_meta_nonce_field', 'widget_ads_nonce');
                if ($updating): ?>
                  <input type="hidden" name="wads_id" value="<?php echo $widgetAd->getID(); ?>"/>
                <?php
                endif;
                ?>
              </div>
            </div>
            <div class="wads-content">

              <p>
                <strong>Widget Ad ID: </strong>
                &nbsp;&nbsp;
                <?php
                  if ($updating):
                    echo strval($widgetAd->getID());
                  else:
                ?>
                  <em>save this widget to generate ID</em>
                <?php endif; ?>
              </p>

              <p>
                <strong>Link URL for Ad:</strong>
                <br/>
                <input type="text" value="<?php echo ($updating) ? $widgetAd->getLink(): ''; ?>" name="wads_url" placeholder="http://..."/>
              </p>

              <p>
                <strong>Top Text:</strong>
                <br/>
                <textarea name="wads_top_text"><?php echo ($updating) ? $widgetAd->getTopText(): ''; ?></textarea>
              </p>

              <p>
                <strong>Overlay Text: (<em>displayed over image</em>)</strong>
                <br/>
                <textarea name="wads_overlay_text"><?php echo ($updating) ? $widgetAd->getOverlayText(): ''; ?></textarea>
              </p>

              <p>
                <strong>Bottom Text:</strong>
                <br/>
                <textarea name="wads_bottom_text"><?php echo ($updating) ? $widgetAd->getBottomText(): ''; ?></textarea>
              </p>

            </div>
          </div>
          <p style="padding-top:10px;border-top:1px dashed #e1e1e1;">
            <input type="submit" name="wads_save" value="Save" class="button-primary"/>&nbsp;<a href="admin.php?page=wads_menu" class="button-secondary">Cancel</a>
            <span style="float: right;">
              <a href="widgets.php" class="button-secondary">Go To Widgets</a>
            </span>
          </p>
        </form>
      </div>

    </div>
    <?php
  }

  public static function display_widget($instance)
  {
    $selected = (isset($instance['selectedad'])) ? $instance['selectedad']: 0;
    $widgetAd = new WidgetAd($selected);

    if (!$widgetAd->exists()) {
      return;
    }
    ?>
    <a class="wads-link" href="<?php echo $widgetAd->getLink(); ?>">
      <div class="wads-top-text">
        <?php echo $widgetAd->getTopText(); ?>
      </div>
      <?php if ($widgetAd->hasImage()): ?>
      <br/>
      <div class="wads-image" style="background-image:url('<?php echo esc_url($widgetAd->getImageURL()); ?>');width:<?php echo $widgetAd->getImageWidth(); ?>px;height:<?php echo $widgetAd->getImageHeight(); ?>px;background-size:<?php echo $widgetAd->getImageWidth(); ?>px <?php echo $widgetAd->getImageHeight(); ?>px;">
        <?php
        $overlayText = $widgetAd->getOverlayText();
        if (!empty($overlayText)):
        ?>
          <span class="wads-overlay"><?php echo $overlayText; ?></span>
        <?php else: ?>
          &nbsp;
        <?php endif; ?>
      </div>
      <br/>
      <?php endif; ?>
      <div class="wads-bottom-text">
        <?php echo $widgetAd->getBottomText(); ?>
      </div>
    </a>
   
    <?php
  }

  public static function add_notice($notice)
  {
    self::$notice = $notice;
  }

  public static function show_admin_notices()
  {
    if (isset($_GET['just_added']) && $_GET['just_added'] == 'yes') {
      ?>
      <div class="updated">
        <p>Successfully Saved Widget Ad</p>
      </div>
      <?php
    }

    if (is_array(self::$notice) && count(self::$notice) == 2) {
      ?>
      <div class="<?php echo self::$notice[0]; ?>">
        <p><?php echo self::$notice[1]; ?></p>
      </div>
      <?php
    }
  }

}
