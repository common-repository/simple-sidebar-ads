<?php
// This file may not be executed directly
if (!defined('ABSPATH')) {
  exit();
}

class WidgetAd
{

  private $ID;
  private $link;
  private $image;
  private $topText;
  private $bottomText;
  private $overlayText;
  private $imageDimensions;

  public function __construct($ID=0)
  {
    $this->ID = $ID;
    $this->refresh();
  }

  /**
   * Insert/Update the WidgetAd data to the post and postmeta tables.
   * Returns true if the procedure succeeds, else false.
   *
   * @return boolean
   */
  public function save()
  {
    if ($this->exists()) {
      update_post_meta($this->ID, 'link_url', $this->getLink());
      update_post_meta($this->ID, 'linked_image', $this->getImage());
      update_post_meta($this->ID, 'image_dimensions', $this->getImageDimensions());
      update_post_meta($this->ID, 'overlay_text', $this->getOverlayText());
      update_post_meta($this->ID, 'top_text', $this->getTopText());
      update_post_meta($this->ID, 'bottom_text', $this->getBottomText());

      return true;
    } else {
      $post = array(
        'post_content' => 'WIDGET_AD',
        'post_title'   => 'WIDGET_AD',
        'post_status'  => 'publish',
        'post_type'    => 'widget_ad'
      );
      $theID = wp_insert_post($post);

      if (!$theID) {
        return false;
      }

      $this->ID = $theID;
      return $this->save();
    }
  }

  /**
   * Re/populate the WidgetAd properties with the values from
   * the posts and postmeta tables.
   *
   * @return void
   */
  public function refresh()
  {
    if ($this->exists()) {
      $link = get_post_meta($this->ID, 'link_url', true);
      $this->setLink($link);
      $image = get_post_meta($this->ID, 'linked_image', true);
      if (!empty($image)) {
        $this->setImage($image);
        $dimensions = get_post_meta($this->ID, 'image_dimensions', true);
        $this->setImageDimensions($dimensions);
        $overlay = get_post_meta($this->ID, 'overlay_text', true);
        $this->setOverlayText($overlay);
      }
      $topText = get_post_meta($this->ID, 'top_text', true);
      $this->setTopText($topText);
      $bottomText = get_post_meta($this->ID, 'bottom_text', true);
      $this->setBottomText($bottomText);
    }
  }

  /**
   * Returns true if the WidgetAd instance has an image.
   *
   * @return boolean
   */
   public function hasImage()
   {
     if (isset($this->image) && $this->image > 0) {
       return true;
     } else {
       return false;
     }
   }

  /**
   * Returns true if the WidgetAd instance, as identified by $ID,
   * exists in the posts table. The post_type column must also be
   * 'widget_ad' to return true.
   *
   * @return boolean
   */
  public function exists()
  {
    // No sense in running a query here.
    if ($this->ID < 1) {
      $this->ID = 0;
      return false;
    } else {
      $thePost = get_post($this->ID);

      if (empty($thePost) || $thePost->post_type !== 'widget_ad') {
        $this->ID = 0;
        return false;
      }

      return true;
    }
  }

  /**
   * Returns the URL of the attachment associated with the
   * WidgetAd instance or void if the instance has no image.
   *
   * @return String
   */
  public function getImageURL()
  {
    if ($this->hasImage()) {
      return wp_get_attachment_url($this->getImage());
    }
  }

  // Setters and Getters

  /**
   * Set $this->link of the instance; should be a valid URL.
   *
   * @return void
   */
  public function setLink($link)
  {
    $this->link = $link;
  }

  /**
   * Set $this->image of the instance; should be the row ID of
   * an image attachment.
   *
   * @return void
   */
  public function setImage($attachmentID)
  {
    $this->image = $attachmentID;
  }

  /**
   * Set $this->topText of the instance.
   *
   * @return void
   */
  public function setTopText($topText)
  {
    $this->topText = $topText;
  }

  /**
   * Set $this->bottomText of the instance.
   *
   * @return void
   */
  public function setBottomText($bottomText)
  {
    $this->bottomText = $bottomText;
  }

  /**
   * Set $this->overlayText of the instance.
   *
   * @return void
   */
  public function setOverlayText($overlayText)
  {
    $this->overlayText = $overlayText;
  }

  /**
   * Set $this->imageDimensions of the instance.
   *
   * @return void
   */
  public function setImageDimensions($imageDimensions)
  {
    $this->imageDimensions = $imageDimensions;
  }

  /**
   * Returns the ID of the WidgetAd instance. An ID
   * of 0 indicates that the WidgetAd has not been saved.
   *
   * @return int
   */
  public function getID()
  {
    return $this->ID;
  }

  /**
   * Returns the link URL for the WidgetAd.
   *
   * @return string
   */
  public function getLink()
  {
    return $this->link;
  }

  /**
   * Returns the attachment ID associated with this WidgetAd
   * instance.
   *
   * @return int
   */
  public function getImage()
  {
    return $this->image;
  }

  /**
   * Returns the top text.
   *
   * @return string
   */
  public function getTopText()
  {
    return $this->topText;
  }

  /**
   * Returns the bottom text.
   *
   * @return string
   */
  public function getBottomText()
  {
    return $this->bottomText;
  }

  /**
   * Returns the overlay text.
   *
   * @return string
   */
  public function getOverlayText()
  {
    return $this->overlayText;
  }

  /**
   * Returns an array with the first element the image
   * width and the second the image height.
   *
   * @return array()
   */
  public function getImageDimensions()
  {
    return $this->imageDimensions;
  }

  public function getImageWidth()
  {
    $dimensions = $this->getImageDimensions();
    return $dimensions[0];
  }

  public function getImageHeight()
  {
    $dimensions = $this->getImageDimensions();
    return $dimensions[1];
  }

  /**
   * Get all of the widget ads from the posts table.
   *
   * @return array()
   */
  public static function get_all()
  {
    $posts = get_posts(array(
      'numberposts' => -1,
      'post_type' => 'widget_ad'
    ));

    $ads = array();

    if (count($posts) > 0) {
      foreach ($posts as $post) {
        $ads[] = new WidgetAd($post->ID);
      }
    }

    return $ads;

  }

}
