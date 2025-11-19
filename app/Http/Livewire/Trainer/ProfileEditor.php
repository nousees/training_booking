<?php

namespace App\Http\Livewire\Trainer;

use App\Models\TrainerProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfileEditor extends Component
{
    use WithFileUploads;

    public $name;
    public $email;
    public $phone;
    public $timezone;
    public $avatar;

    public $bio = '';
    public $specializations = [];
    public $experience_years = 0;
    public $price_per_hour = 0;
    public $images = [];
    public $supports_online = false;
    public $online_link = '';

    public $avatar_upload;
    public $gallery_uploads = [];

    protected function rules()
    {
        return [
            'name' => ['required','string','max:255'],
            'phone' => ['nullable','string','max:50'],

            'avatar_upload' => ['nullable','image','max:2048'],

            'bio' => ['nullable','string'],
            'specializations' => ['array'],
            'specializations.*' => ['string','max:50'],
            'experience_years' => ['integer','min:0','max:80'],
            'price_per_hour' => ['numeric','min:0'],
            'gallery_uploads.*' => ['image','max:4096'],
            'supports_online' => ['boolean'],
            'online_link' => ['nullable','string','max:500'],
        ];
    }

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->timezone = $user->timezone ?? 'Europe/Moscow';
        $this->avatar = $user->avatar;

        /** @var TrainerProfile $profile */
        $profile = $user->trainerProfile()->firstOrCreate([ 'user_id' => $user->id ], [
            'bio' => '', 'specializations' => [], 'experience_years' => 0, 'price_per_hour' => 0, 'images' => [],
        ]);

        $this->bio = $profile->bio ?? '';
        $this->specializations = is_array($profile->specializations) ? $profile->specializations : [];
        $this->experience_years = $profile->experience_years ?? 0;
        $this->price_per_hour = (float) ($profile->price_per_hour ?? 0);
        $this->images = is_array($profile->images) ? $profile->images : [];
        $this->supports_online = (bool)($profile->supports_online ?? false);
        $this->online_link = (string)($profile->online_link ?? '');
    }

    public function save()
    {
        $this->validate();

        $user = Auth::user();

        if ($this->avatar_upload) {
            $path = $this->avatar_upload->store('avatars', 'public');
            $this->avatar = $path;
        }

        $user->update([
            'name' => $this->name,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
        ]);

        $profile = $user->trainerProfile()->firstOrCreate(['user_id' => $user->id]);


        $gallery = $this->images ?: [];
        foreach ($this->gallery_uploads as $upload) {
            $gallery[] = $upload->store('trainer_gallery', 'public');
        }

        $profile->update([
            'bio' => $this->bio,
            'specializations' => array_values(array_filter($this->specializations)),
            'experience_years' => (int) $this->experience_years,
            'price_per_hour' => $this->price_per_hour,
            'images' => $gallery,
            'supports_online' => (bool)$this->supports_online,
            'online_link' => $this->supports_online ? ($this->online_link ?: null) : null,
        ]);

        session()->flash('message', __('Профиль обновлён'));
    }

    public function removeImage($index)
    {
        if (isset($this->images[$index])) {

            array_splice($this->images, $index, 1);
        }
    }

    public function render()
    {
        $timezones = \DateTimeZone::listIdentifiers();
        return view('livewire.trainer.profile-editor', compact('timezones'));
    }
}
