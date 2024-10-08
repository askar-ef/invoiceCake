<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Transaction[] $transactions
 * @var \App\Model\Entity\Purchase[] $purchases
 */
?>

<div class="companyReport index content">
    <h3 class="mb-4"><?= __('Company Report') ?></h3>

    <!-- Form to choose the start and end dates -->
    <?= $this->Form->create(null, ['url' => ['action' => 'index'], 'class' => 'mb-4']) ?>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="start-date" class="form-label"><?= __('Start Date') ?>:</label>
                <?= $this->Form->control('start_date', [
                    'type' => 'date',
                    'label' => false,
                    'class' => 'form-control',
                    'required' => true,
                    'default' => date('Y-m-d')
                ]) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="end-date" class="form-label"><?= __('End Date') ?>:</label>
                <?= $this->Form->control('end_date', [
                    'type' => 'date',
                    'label' => false,
                    'class' => 'form-control',
                    'required' => true,
                    'default' => date('Y-m-d')
                ]) ?>
            </div>
        </div>
        <div class="col-md-4">
            <?= $this->Form->submit(__('Export to Excel'), [
                'name' => 'exportExcel',
                'class' => 'btn btn-primary me-2'
            ]) ?>
            <?= $this->Form->submit(__('Export to HTML'), [
                'name' => 'exportHtml',
                'class' => 'btn btn-success'
            ]) ?>
        </div>
    </div>
    <?= $this->Form->end() ?>

    <h4 class="mt-4"><?= __('Transactions') ?></h4>
    <div class="table-responsive">
        <table id="transactionsTable" class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th><?= __('ID') ?></th>
                    <th><?= __('Customer') ?></th>
                    <th><?= __('Date') ?></th>
                    <th><?= __('Amount') ?></th>
                    <th><?= __('Code') ?></th>
                    <th><?= __('Created By') ?></th>
                    <th><?= __('Modified By') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?= $this->Number->format($transaction->id) ?></td>
                        <td><?= h($transaction->customer->name) ?></td>
                        <td><?= h($transaction->transaction_date) ?></td>
                        <td><?= $this->Number->format($transaction->amount) ?></td>
                        <td><?= h($transaction->code) ?></td>
                        <td><?= $transaction->createdByUser ? h($transaction->createdByUser->email) : '' ?></td>
                        <td><?= $transaction->modifiedByUser ? h($transaction->modifiedByUser->email) : '' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <h4 class="mt-4"><?= __('Purchases') ?></h4>
    <div class="table-responsive">
        <table id="purchasesTable" class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th><?= __('ID') ?></th>
                    <th><?= __('Supplier') ?></th>
                    <th><?= __('Date') ?></th>
                    <th><?= __('Amount') ?></th>
                    <th><?= __('Created By') ?></th>
                    <th><?= __('Modified By') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($purchases as $purchase): ?>
                    <tr>
                        <td><?= $this->Number->format($purchase->id) ?></td>
                        <td><?= h($purchase->supplier->name) ?></td>
                        <td><?= h($purchase->purchase_date) ?></td>
                        <td><?= $this->Number->format($purchase->amount) ?></td>
                        <td><?= $purchase->createdByUser ? h($purchase->createdByUser->email) : '' ?></td>
                        <td><?= $purchase->modifiedByUser ? h($purchase->modifiedByUser->email) : '' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Load DataTables JS and CSS -->
<?= $this->Html->css('https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css', ['block' => true]) ?>
<?= $this->Html->script('https://code.jquery.com/jquery-3.5.1.min.js', ['block' => true]) ?>
<?= $this->Html->script('https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js', ['block' => true]) ?>

<!-- Initialize DataTables -->
<?= $this->Html->scriptBlock('
    $(document).ready(function() {
        $("#transactionsTable").DataTable();
        $("#purchasesTable").DataTable();
    });
', ['block' => true]) ?>