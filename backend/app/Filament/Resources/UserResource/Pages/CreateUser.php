<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

	protected function mutateFormDataBeforeCreate( array $data ): array
	{
		// The user chooses their own password through the invitation link
		$data['password'] = Str::random(64);

		return $data;
	}

	protected function afterCreate(): void
	{
		$this->record->sendInvitation();
	}

	protected function getCreatedNotification(): ?Notification
	{
		return Notification::make()
			->success()
			->title(__('User created'))
			->body(__('An invitation was sent to :email.', ['email' => $this->record->email]));
	}

	protected function getRedirectUrl(): string
	{
		return $this->getResource()::getUrl('index');
	}
}
