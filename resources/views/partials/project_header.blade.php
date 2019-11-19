<div class="card-body">
    <div class="row m-b-10">
        <div class="col-md-12 m-b-5">
            <div class="row"><small>project</small></div>
        </div>
        <div class="col-md-12 m-b-5">
            <div class="row">
                <div class="col-md-3"><b>Name</b></div>
                <div class="col-md-9">
                    {{ $project->name }}
                </div>
            </div>
        </div>
        <div class="col-md-12 m-b-5">
            <div class="row">
                <div class="col-md-3"><b>Credential Fingerprint</b></div>
                <div class="col-md-9">
                    {{ $project->credential->fingerprint }}
                </div>
            </div>
        </div>
        <div class="col-md-12 m-b-5">
            <div class="row">
                <div class="col-md-3"><b>Keep Old Build</b></div>
                <div class="col-md-9">
                    {{ $project->keep_build }}
                </div>
            </div>
        </div>
        <div class="col-md-12 m-b-5">
            <div class="row">
                <div class="col-md-3"><b>Build Timeout</b></div>
                <div class="col-md-9">
                    {{ $project->timeout }}s
                </div>
            </div>
        </div>
        <div class="col-md-12 m-b-5">
            <div class="row"><small>repository</small></div>
        </div>
        <div class="col-md-12 m-b-5">
            <div class="row">
                <div class="col-md-3"><b>Repository</b></div>
                <div class="col-md-9">
                    <i class="fab fa-{{ \Fikrimi\Pipe\Enum\Repository::$fabLogo[$project->repository] }}"></i>
                    {{ \Fikrimi\Pipe\Enum\Repository::$names[$project->repository] }}
                </div>
            </div>
        </div>
        <div class="col-md-12 m-b-5">
            <div class="row">
                <div class="col-md-3"><b>Namespace</b></div>
                <div class="col-md-9">{{ $project->namespace }}</div>
            </div>
        </div>
        <div class="col-md-12 m-b-5">
            <div class="row">
                <div class="col-md-3"><b>Default Branch</b></div>
                <div class="col-md-9">{{ $project->branch }}</div>
            </div>
        </div>
        <div class="col-md-12 m-b-5">
            <div class="row">
                <div class="col-md-3"><b>WebHook URL</b></div>
                <div class="col-md-9"><span id="webhook-url">{{ url('pipe/webhook/' . $project->id) }}</span> <a href="#" onclick="copyToClipboard(document.getElementById('webhook-url').textContent)"><i class="fas fa-copy"></i></a></div>
            </div>
        </div>
        <div class="col-md-12 m-b-5">
            <div class="row"><small>host</small></div>
        </div>
        <div class="col-md-12 m-b-5">
            <div class="row">
                <div class="col-md-3"><b>Host</b></div>
                <div class="col-md-9">{{ $project->host }}</div>
            </div>
        </div>
        <div class="col-md-12 m-b-5">
            <div class="row">
                <div class="col-md-3"><b>Deploy Dir</b></div>
                <div class="col-md-9">{{ $project->dir_deploy }}</div>
            </div>
        </div>
        <div class="col-md-12 m-b-5">
            <div class="row">
                <div class="col-md-3"><b>Workspace Dir</b></div>
                <div class="col-md-9">{{ $project->dir_workspace }}</div>
            </div>
        </div>
    </div>
</div>

<script>
    const copyToClipboard = str => {
        const el = document.createElement('textarea');  // Create a <textarea> element
        el.value = str;                                 // Set its value to the string that you want copied
        el.setAttribute('readonly', '');                // Make it readonly to be tamper-proof
        el.style.position = 'absolute';
        el.style.left = '-9999px';                      // Move outside the screen to make it invisible
        document.body.appendChild(el);                  // Append the <textarea> element to the HTML document
        const selected =
            document.getSelection().rangeCount > 0        // Check if there is any content selected previously
                ? document.getSelection().getRangeAt(0)     // Store selection if found
                : false;                                    // Mark as false to know no selection existed before
        el.select();                                    // Select the <textarea> content
        document.execCommand('copy');                   // Copy - only works as a result of a user action (e.g. click events)
        document.body.removeChild(el);                  // Remove the <textarea> element
        if (selected) {                                 // If a selection existed before copying
            document.getSelection().removeAllRanges();    // Unselect everything on the HTML document
            document.getSelection().addRange(selected);   // Restore the original selection
        }
        alert('copied to clipboard')
    };
</script>
