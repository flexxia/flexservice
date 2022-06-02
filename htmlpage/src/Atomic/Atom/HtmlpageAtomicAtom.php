<?php

namespace Drupal\htmlpage\Atomic\Atom;

use Drupal\htmlpage\Atomic\HtmlpageAtomic;

/**
 * Class HtmlpageAtomicAtom.
 \Drupal::service('htmlpage.atomic.atom')->demo();
 */
class HtmlpageAtomicAtom extends HtmlpageAtomic {

  /**
   * Constructs a new HtmlpageAtomicAtom object.
   */
  public function __construct() {

  }

  /**
   *
   */
  public function generateUniqueId() {
    $output = hexdec(substr(uniqid(NULL, TRUE), 15, 8));
    return $output;
  }


  /**
   *
   */
  public function getProgramImage($program_term = NULL) {
    $output = '';

    if (isset($program_term->field_program_image->entity)) {
      $image = [
        '#theme' => 'image_style',
        '#style_name' => 'medium',
        '#uri' => $program_term->get('field_program_image')->entity->getFileUri(),
      ];
      $output .= '<div class="col-xs-12">';
        $output .= '<div class="program-header-image-wrapper">';
          $output .= \Drupal::service('renderer')->render($image);
        $output .= '</div>';
      $output .= '</div>';
    }

    return $output;
  }

  /**
   *
   */
  public function getBlockTileMeetingShareLink($meeting_nid = NULL) {
    $output = '';
    $output .= '<div class="dropdown float-right">';
      $output .= '<button class="btn btn-default bg-009ddf color-fff dropdown-toggle min-width-20 padding-10" type="button" id="tile-meeting-share-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">';
        $output .= '<span class="margin-12">SHARE</span>';
        $output .= '<span class="caret"></span>';
      $output .= '</button>';
      $output .= '<ul class="dropdown-menu" aria-labelledby="tile-meeting-share-link" class="color-00a9e0 bg-ffffff">';
        $output .= '<li>';
          $output .= '<span class="margin-left-12">';
            $output .= '<a href="mailto:?subject=Share Link&amp;body=' . $this->getShareMeetingLink($meeting_nid) . '" class="color-000 text-decoration-none">';
              $output .= 'Share Link';
            $output .= '</a>';
          $output .= '</span>';
        $output .= '</li>';
        $output .= '<li>';
          $output .= '<span class="margin-left-12">';
            $output .= '<a href="' . $this->getHtmlPdfDownloadLinkForMeeting($meeting_nid) . '" class="color-000 text-decoration-none">';
              $output .= 'Download PDF';
            $output .= '</a>';
          $output .= '</span>';
        $output .= '</li>';

      $output .= '</ul>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function getBlockTileProgramShareLink($program_tid = NULL, $mail_link = FALSE) {
    $output = '';
    $output .= '<div class="dropdown float-right">';
      $output .= '<button class="btn btn-default bg-009ddf color-fff dropdown-toggle min-width-20 padding-10" type="button" id="tile-meeting-share-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">';
        $output .= '<span class="margin-12">SHARE</span>';
        $output .= '<span class="caret"></span>';
      $output .= '</button>';
      $output .= '<ul class="dropdown-menu" aria-labelledby="tile-meeting-share-link" class="color-00a9e0 bg-ffffff">';
        if ($mail_link) {
          $output .= '<li>';
            $output .= '<span class="margin-left-12">';
              $output .= '<a href="mailto:?subject=Share Link&amp;body=' . $this->getShareProgramLink($program_tid) . '" class="color-000 text-decoration-none">';
                $output .= 'Share Link';
              $output .= '</a>';
            $output .= '</span>';
          $output .= '</li>';
        }
        $output .= '<li>';
          $output .= '<span class="margin-left-12">';
            $output .= '<a href="' . $this->getHtmlPdfDownloadLinkForProgram($program_tid) . '" class="color-000 text-decoration-none">';
              $output .= 'Download PDF';
            $output .= '</a>';
          $output .= '</span>';
        $output .= '</li>';
      $output .= '</ul>';
    $output .= '</div>';

    return $output;
  }

  /**
   *
   */
  public function getShareMeetingLink($meeting_nid = NULL) {
    $output = NULL;

    $start = \Drupal::service('flexinfo.setting.service')->userStartTime();
    $end = \Drupal::service('flexinfo.setting.service')->userEndTime();

    $app_root = \Drupal::hasService('app.root') ? \Drupal::root() : DRUPAL_ROOT;

    if (file_exists($app_root . '/sites/default/settings.local.php')) {
      $output .= \Drupal::request()->getHttpHost() . base_path();
    }
    else {
      $output .= 'https://' . \Drupal::request()->getHost() . '/';
    }
    $output .= 'htmlguest/meeting/page/' . $meeting_nid . '/' . $start . '/' . $end;

    return $output;
  }

  /**
   *
   */
  public function getShareProgramLink($program_tid = NULL) {
    $output = NULL;

    $start = \Drupal::service('flexinfo.setting.service')->userStartTime();
    $end = \Drupal::service('flexinfo.setting.service')->userEndTime();

    $app_root = \Drupal::hasService('app.root') ? \Drupal::root() : DRUPAL_ROOT;

    if (file_exists($app_root . '/sites/default/settings.local.php')) {
      $output .= \Drupal::request()->getHttpHost() . base_path();
    }
    else {
      $output .= 'https://' . \Drupal::request()->getHost() . '/';
    }
    $output .= 'htmlguest/program/page/' . $program_tid . '/' . $start . '/' . $end;

    return $output;
  }

  /**
   *
   */
  public function getHtmlPdfDownloadLinkForProgram($program_tid = NULL) {
    $start = \Drupal::service('flexinfo.setting.service')->userStartTime();
    $end = \Drupal::service('flexinfo.setting.service')->userEndTime();
    $app_root = \Drupal::hasService('app.root') ? \Drupal::root() : DRUPAL_ROOT;

    $pdf_link = NULL;
    if (file_exists($app_root . '/sites/default/settings.local.php')) {
      $pdf_link .= base_path();
    }
    else {
      $pdf_link .= 'https://';
      $pdf_link .= \Drupal::request()->getHost() . '/';
    }
    $pdf_link .= 'htmlpage/program/print_pdf/' . $program_tid . '/' . $start . '/' . $end;;

    return $pdf_link;
  }

  /**
   *
   */
  public function getHtmlPdfDownloadLinkForMeeting($meeting_nid = NULL) {
    $start = \Drupal::service('flexinfo.setting.service')->userStartTime();
    $end = \Drupal::service('flexinfo.setting.service')->userEndTime();
    $app_root = \Drupal::hasService('app.root') ? \Drupal::root() : DRUPAL_ROOT;

    $pdf_link = NULL;
    if (file_exists($app_root . '/sites/default/settings.local.php')) {
      $pdf_link .= base_path();
    }
    else {
      $pdf_link .= 'https://';
      $pdf_link .= \Drupal::request()->getHost() . '/';
    }
    $pdf_link .= 'htmlpage/meeting/print_pdf/' . $meeting_nid . '/' . $start . '/' . $end;

    return $pdf_link;
  }


}
