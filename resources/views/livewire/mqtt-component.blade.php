<div>
    <div>
        <h3>Publish a Message</h3>
        <form wire:submit="publishMessage">
            <div>
                <label for="topic">Topic</label>
                <input type="text" id="topic" wire:model="topic" />
            </div>

            <div>
                <label for="message">Message</label>
                <input type="text" id="message" wire:model="message" />
            </div>

            <button type="submit">Publish</button>
        </form>
    </div>

    <div>
        <h3>Subscribe to a Topic</h3>
        <form wire:submit="subscribeToTopic">
            <div>
                <label for="subscribe_topic">Topic</label>
                <input type="text" id="subscribe_topic" wire:model="topic" />
            </div>
            <button type="submit">Subscribe</button>
        </form>
    </div>

    <div>
        @if (session()->has('message'))
            <div>{{ session('message') }}</div>
        @endif
    </div>

    <button wire:click="addMessage('New message')">Add Message</button>

    @foreach ($messages as $m)
        <p>{{ $m }}</p>
    @endforeach
</div>
