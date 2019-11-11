<div class="form-group">
    <label for="name">Name</label>
    <input name="name" type="text" class="form-control" id="name" placeholder="Some catchy name" value="{{ $stack->name }}">
</div>
<div class="form-group">
    <label for="description">Description</label>
    <input name="description" type="text" class="form-control" id="description" placeholder="And its description" value="{{ $stack->description }}">
</div>
<div class="form-group">
    <label for="commands">Commands</label>
    <textarea name="commands" id="commands" cols="30" rows="10" class="form-control" placeholder="./vendor/bin/phpunit">{{ implode(PHP_EOL, $stack->commands ?? []) }}</textarea>
    <small id="command-help" class="form-text text-muted">Use newline to add new commands. As long as this raw command can be executed on your server, its safe to add it.</small>
</div>
<button type="submit" class="btn btn-primary">Submit</button>
