<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\Content;
use Illuminate\Support\Facades\DB;
// use Illuminate\Http\RedirectResponse;

use function PHPUnit\Framework\isNull;

class SpritTheBill extends Controller
{
    /**
     * トップ画面を表示する
     * @return view
     */
    public function showTop() {                
        $persons = Person::get();
        return view('index.top', ['persons' => $persons]);
    }


    /**
     * 追加ボタン後の処理
     * @param request
     * @return view
     */
    public function exeStore(Request $request) {

        $request->validate([
            'name' => 'required',
        ]);

        $person = $request->all();
        DB::beginTransaction();
        try {
            Person::create($person);
            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            abort(500);
        }
        return redirect(route('top'));
    }

    /**
     * 削除機能
     * @param id
     * @return view
     */
    public function exeDelete($id) {
        $person = Person::find($id);
        if (!empty($id)) {
            $person->delete();
            return redirect(route('top'))->with('success_msg','削除しました。');
        }
        return redirect(route('top'))->with('err_msg','削除失敗しました。');
    }

    /**
     * 内容入力ページへ遷移
     * @param id
     * @return view
     */
    public function showAmount($id) {
        $persons = Person::get();
        // dd($persons);
        $person = Person::find($id);
        if (empty($person)) {
            return redirect(route('index.top'))->with('err_msg','idが見つかりませんでした。');
        }
        return view('index.enterAmount', ['person' => [$person], 'persons'=>$persons]);
    }


    /**
     * 内容追加処理
     * @param name
     * @return view
     */
    public function exeAdd($name) {
        $PersonName = Person::where('name', $name)->first();
        $person = Person::find($PersonName->id);
        // dd($person);
        DB::beginTransaction();
        try {
            Content::create([
                'name'=>$PersonName->name,
                'content'=> "",
                'cost'=> 0,
            ]);
            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            abort(500);
        }
        $datas = Content::where('name', $name)->get();
        // dd($datas);
        return view('index.enterAmount', ['person' => [$person], 'datas' => $datas]);
    }
}

