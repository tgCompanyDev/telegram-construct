<?php
namespace Valibool\TelegramConstruct\Models\File\Traits;


use App\Models\File\Attachment;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Valibool\TelegramConstruct\Models\File\TgConstructAttachment;

/**
 * This trait is used to relate or attach multiple files with Eloquent models.
 */
trait Attachable
{
    /**
     * Get all the attachments associated with the given model.
     *
     * @param string|null $group
     *
     * @return MorphToMany
     */
    public function attachment(?string $group = null): MorphToMany
    {
        $query = $this->morphToMany(
            TgConstructAttachment::class,
            'tg_construct_attachmentable',
            'tg_construct_attachmentable',
            'tg_construct_attachmentable_id',
            'tg_construct_attachment_id'
        );

        if ($group !== null) {
            $query->where('group', $group);
        }

        return $query
            ->orderBy('sort');
    }
}
