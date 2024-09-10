<?php

/**
 * @var \App\View\AppView $this
 */
?>
<h1>Download Report</h1>

<?= $this->Form->create(null, ['type' => 'post']) ?>
<?= $this->Form->control('startdate', ['label' => 'Start Date', 'type' => 'date']) ?>
<?= $this->Form->control('enddate', ['label' => 'End Date', 'type' => 'date']) ?>
<?= $this->Form->control('format', [
    'type' => 'select',
    'label' => 'Export Format',
    'options' => ['excel' => 'Excel', 'html' => 'HTML'],
]) ?>
<?= $this->Form->button(__('Download')) ?>
<?= $this->Form->end() ?>