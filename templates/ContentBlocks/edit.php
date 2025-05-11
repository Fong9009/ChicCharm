<?php
/**
 * @var \App\View\AppView $this
 * @var \ContentBlocks\Model\Entity\ContentBlock $contentBlock
 */

$this->assign('title', 'Edit Content Block - Content Blocks');

$this->Html->script('ContentBlocks.ckeditor/ckeditor', ['block' => true]);

$this->Html->css('ContentBlocks.content-blocks', ['block' => true]);
?>

<style>
    .ck-editor__editable_inline {
        min-height: 25rem; /* CKEditor field minimal height */
    }
</style>
<div class="admin-background">
    <div class="contacts index content">
        <div class="column">
            <div class="enquiry-form">
                <h3 class="content-blocks--form-heading"><?= $contentBlock->label ?></h3>
                <div class="content-blocks--form-description">
                    <p class="fs-5"> Description: <?= $contentBlock->description ?> </p>
                    <p class="fs-6">Adjust accordingly to what you require.</p>
                </div>
                <?= $this->Form->create($contentBlock, ['type' => 'file']) ?>
                <?php
                if ($contentBlock->type === 'text') {
                    echo $this->Form->control('value', [
                        'type' => 'textarea',
                        'class' => 'form-control',
                        'id' => 'value',
                        'value' => html_entity_decode($contentBlock->value),
                        'label' => false,
                        'rows' => '6',
                        'maxlength' => 2000,
                    ]);
                    ?>
                    <div class="row">
                        <div id="message-char-count" class="char-count-display text-muted text-start small mb-2"></div>
                    </div>
                <?php
                } else if ($contentBlock->type === 'html') {
                    echo $this->Form->control('value', [
                        'type' => 'textarea',
                        'class' => 'form-control',
                        'label' => false,
                        'id' => 'content-value-input',
                        'maxlength' => 2000,
                    ]);
                    ?>
                    <!-- Load CKEditor. -->
                    <script>
                        /*
                        Create our CKEditor instance in a DOMContentLoaded event callback, to ensure
                        the library is available when we call `create()`.
                        Fixes https://github.com/ugie-cake/cakephp-content-blocks/issues/4.
                        */
                        document.addEventListener("DOMContentLoaded", (event) => {
                            CKSource.Editor.create(
                                document.getElementById('content-value-input'),
                                {
                                    toolbar: [
                                        "heading", "|",
                                        "bold", "italic", "underline", "|",
                                        "bulletedList", "numberedList", "|",
                                        "alignment", "blockQuote", "|",
                                        "indent", "outdent", "|",
                                        "link", "|",
                                        "insertTable", "imageInsert", "mediaEmbed", "horizontalLine", "|",
                                        "removeFormat", "|",
                                        "sourceEditing", "|",
                                        "undo", "redo",
                                    ],
                                    simpleUpload: {
                                        uploadUrl: <?= json_encode($this->Url->build(['action' => 'upload'])) ?>,
                                        headers: {
                                            'X-CSRF-TOKEN': <?= json_encode($this->request->getAttribute('csrfToken')) ?>,
                                        }
                                    }
                                }
                            ).then(editor => {
                                console.log(Array.from( editor.ui.componentFactory.names() ));
                            });
                        });
                    </script>
                    <?php
                } else if ($contentBlock->type === 'image') {

                    if ($contentBlock->value) {
                        echo $this->Html->image($contentBlock->value, ['class' => 'content-blocks--image-preview']);
                    }

                    echo $this->Form->control('value', [
                        'class' => 'form-control',
                        'type' => 'file',
                        'accept' => 'image/*',
                        'label' => false,
                    ]);
                }

                ?>
                <div class="content-blocks--form-actions">
                    <?= $this->Form->button(__('Save'), ['class' => 'btn btn-primary']) ?>
                    <?= $this->Html->link('Cancel', ['action' => 'index'],
                        ['class' => 'btn btn-primary',
                        'style' => 'background-color: #6c757d; border-color: #6c757d; color: white; transition: all 0.3s;',
                        'onmouseover' => 'this.style.backgroundColor = "#5a6268"; this.style.borderColor = "#545b62";',
                        'onmouseout' => 'this.style.backgroundColor = "#6c757d"; this.style.borderColor = "#6c757d";']) ?>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
        <?php $this->append('script'); ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const messageTextarea = document.getElementById('value');
                const charCountDisplay = document.getElementById('message-char-count');
                const maxLength = messageTextarea ? parseInt(messageTextarea.getAttribute('maxlength'), 10) : 0;

                function updateCharCount() {
                    if (!messageTextarea || !charCountDisplay || !maxLength) return;

                    const currentLength = messageTextarea.value.length;
                    const remaining = maxLength - currentLength;

                    charCountDisplay.textContent = `${currentLength}/${maxLength}`;

                    if (remaining < 0) {
                        charCountDisplay.style.color = 'red';
                    } else if (remaining < 50) {
                        charCountDisplay.style.color = 'orange';
                    } else {
                        charCountDisplay.style.color = '';
                    }
                }

                if (messageTextarea) {
                    messageTextarea.addEventListener('input', updateCharCount);
                    updateCharCount();
                }
            });
        </script>
        <?php $this->end(); ?>
    </div>
</div>
