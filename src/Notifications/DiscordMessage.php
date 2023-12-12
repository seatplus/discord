<?php

namespace Seatplus\Discord\Notifications;

class DiscordMessage
{
    public static function create(string $body = '', array $embeds = []): self
    {
        return new self($body, $embeds);
    }

    public function __construct(
        public string $body = '',
        public array $embeds = []
    )
    {
    }

    public function toArray(): array
    {
        return [
            'content' => $this->body,
            'embeds' => $this->embeds
        ];
    }

    public function addEmbed(array $embed): self
    {
        $this->embeds[] = $embed;

        return $this;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function setEmbeds(array $embeds): self
    {
        $this->embeds = $embeds;

        return $this;
    }

}
