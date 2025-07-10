<?php

namespace GivePdfReceipt\FormExtension\FormBuilder\DataTransferObjects;

use Give\Framework\Support\Contracts\Arrayable;
use Give\Framework\Support\Contracts\Jsonable;

/**
 * @since 3.1.0
 */
class FormBuilderPdfSettings implements Arrayable, Jsonable
{
    /**
     * @var string
     */
    public $enable;

    /**
     * @var string
     */
    public $generationMethod;

    /**
     * @var string
     */
    public $colorPicker;

    /**
     * @var string
     */
    public $templateId;

    /**
     * @var string
     */
    public $logoUpload;

    /**
     * @var string
     */
    public $companyName;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $addressLine1;

    /**
     * @var string
     */
    public $addressLine2;

    /**
     * @var string
     */
    public $cityStateZip;

    /**
     * @var string
     */
    public $displayWebsiteUrl;

    /**
     * @string
     */
    public $emailAddress;

    /**
     * @string
     */
    public $headerMessage;

    /**
     * @string
     */
    public $footerMessage;

    /**
     * @string
     */
    public $additionalNotes;

    /**
     * @string
     */
    public $customTemplateId;

    /**
     * @string
     */
    public $customTemplateName;

    /**
     * @string
     */
    public $customPageSize;

    /**
     * @string
     */
    public $customPdfBuilder;

    /**
     * @since 3.1.0
     */
    public static function fromArray(array $array): self
    {
        $self = new self();

        $self->enable = $array['enable'] ?? 'global'; // global | enabled | disabled
        $self->generationMethod = $array['generationMethod'] ?? 'set_pdf_templates'; // set_pdf_templates | custom_pdf_builder
        $self->colorPicker = $array['colorPicker'] ?? '#1E8CBE';
        $self->templateId = $array['templateId'] ?? 'default';
        $self->logoUpload = $array['logoUpload'] ?? '';
        $self->name = $array['name'] ?? '';
        $self->companyName = $self->name; // The same value is used for 'name' and 'companyName'
        $self->addressLine1 = $array['addressLine1'] ?? '';
        $self->addressLine2 = $array['addressLine2'] ?? '';
        $self->cityStateZip = $array['cityStateZip'] ?? '';
        $self->displayWebsiteUrl = isset($array['displayWebsiteUrl']) && $array['displayWebsiteUrl'] ? 'on' : '';
        $self->emailAddress = $array['emailAddress'] ?? '';
        $self->headerMessage = $array['headerMessage'] ?? '';
        $self->footerMessage = $array['footerMessage'] ?? '';
        $self->additionalNotes = $array['additionalNotes'] ?? '';
        $self->customTemplateId = $array['customTemplateId'] ?? '';
        $self->customTemplateName = $array['customTemplateName'] ?? '';
        $self->customPageSize = $array['customPageSize'] ?? '';
        $self->customPdfBuilder = $array['customPdfBuilder'] ?? '';

        return $self;
    }

    /**
     * @since 3.1.0
     */
    public static function fromJson(string $json): self
    {
        $self = new self();
        $array = json_decode($json, true);

        return $self::fromArray($array);
    }

    /**
     * @since 3.1.0
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * @since 3.1.0
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray());
    }
}

