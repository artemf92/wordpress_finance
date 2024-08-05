<?php
/**
 * Theme: Flat Bootstrap
 * 
 * The template used for displaying next / previous page links
 *
 * @package flat-bootstrap
 */
?>

<?php

/*
 * If page content split into multiple pages, display links
 */
$total_pages = $args['query']->max_num_pages;
if ($total_pages == 1) return;
?>
<div class="pagination-controls m-b-2">
    <input type="hidden" name="paged" value="<?php echo max(1, $paged); ?>">
    <?php if ($paged > 1): ?>
        <button type="submit" name="paged" value="<?php echo $paged - 1; ?>"><?= __('Previous') ?></button>
    <?php endif; ?>
    
    <?php 
        for ($page = 1; $page <= $total_pages; $page++) {
            $active = ($page == $paged) || ($page == 1 && $paged == 0)  ? 'active' : '';
            echo '<button type="submit" name="paged" value="' . $page . '" class="' . $active . '">' . $page . '</button> ';
        }
    ?>
    
    <?php if ($paged < $total_pages): ?>
        <button type="submit" name="paged" value="<?php echo $paged + 1; ?>"><?= __('Next') ?></button>
    <?php endif; ?>
</div>