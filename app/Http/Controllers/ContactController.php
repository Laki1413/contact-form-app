<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;

class ContactController extends Controller
{
    //お問い合わせフォーム入力ページ
    public function index()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('contact.index', compact('categories', 'tags'));
    }

    //お問い合わせフォーム確認ページ
    public function confirm(StoreContactRequest $request)
    {
        $validated = $request->validated();

        $request->flash();

        $category = Category::find($validated['category_id']);
        $tags = Tag::whereIn('id', $validated['tag_ids'] ?? [])->get();

        return view('contact.confirm', compact(
            'validated',
            'category',
            'tags'
        ));
    }

    //お問い合わせを送信
    public function store(StoreContactRequest $request)
    {
        $validated = $request->validated();

        $tagIds = $validated['tag_ids'] ?? [];
        unset($validated['tag_ids']);

        $contact = Contact::create($validated);

        $contact->tags()->sync($tagIds);

        return redirect('/thanks');
    }


    public function thanks()
    {
        return view('contact.thanks');
    }
}
