<?php
namespace nethaven\invoiced\models;

use nethaven\invoiced\helpers\FileHelper;

use Craft;
use craft\base\Model;
use craft\db\SoftDeleteTrait;
use craft\helpers\ArrayHelper;
use craft\validators\HandleValidator;
use craft\validators\UniqueValidator;

use yii\validators\Validator;

use DateTime;

abstract class BaseTemplate extends Model
{
    use SoftDeleteTrait {
        behaviors as softDeleteBehaviors;
    }


    // Properties
    // =========================================================================

    public ?int $id = null;
    public ?string $name = null;
    public ?string $handle = null;
    public ?int $sortOrder = null;
    public ?DateTime $dateDeleted = null;
    public ?string $uid = null;


    // Public Methods
    // =========================================================================

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getDisplayName();
    }

    /**
     * Gets the display name for the template.
     *
     * @return string
     */
    public function getDisplayName(): string
    {
        if ($this->dateDeleted !== null) {
            return $this->name . '(Trashed)';
        }

        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function defineRules(): array
    {
        $rules = parent::defineRules();

        $rules[] = [['name', 'handle'], 'required'];
        $rules[] = [['name', 'handle'], 'string', 'max' => 255];
        $rules[] = [
            ['handle'],
            HandleValidator::class,
            'reservedWords' => ['id', 'dateCreated', 'dateUpdated', 'uid', 'title'],
        ];
        $rules[] = [
            ['handle'],
            UniqueValidator::class,
            'targetClass' => $this->getRecordClass(),
        ];
        
        return $rules;
    }

    /**
     * Returns the class of the template active record.
     *
     * @return string
     */
    abstract protected function getRecordClass(): string;
}
