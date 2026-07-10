<?php

namespace JeffersonGoncalves\Newsletter\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use JeffersonGoncalves\Newsletter\Actions\ConfirmEmailGroupSubscriptionAction;
use JeffersonGoncalves\Newsletter\Actions\SubscribeToEmailGroupAction;
use JeffersonGoncalves\Newsletter\Actions\UnsubscribeEmailGroupMemberAction;
use JeffersonGoncalves\Newsletter\Models\EmailGroupMember;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request, SubscribeToEmailGroupAction $action): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'group' => ['nullable', 'string'],
        ]);

        $action->handle($data['email'], $data['group'] ?? config('newsletter.default_email_group'));

        return back()->with('status', __('newsletter::emails.subscribed'));
    }

    public function confirm(EmailGroupMember $emailGroupMember, ConfirmEmailGroupSubscriptionAction $action): View
    {
        $action->handle($emailGroupMember);

        return view('newsletter::confirmed', compact('emailGroupMember'));
    }

    public function unsubscribe(EmailGroupMember $emailGroupMember, UnsubscribeEmailGroupMemberAction $action): View
    {
        $action->handle($emailGroupMember);

        return view('newsletter::unsubscribed', compact('emailGroupMember'));
    }
}
