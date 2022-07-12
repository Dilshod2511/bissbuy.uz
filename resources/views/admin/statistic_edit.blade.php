
<div class="row">


    {{-- @if(session()->has('success'))
        <div >Транзакция прошел успешно</div>
    @endif --}}
    @if($errors->any())
    <div class="alert alert-success" role="alert">{{$errors->first()}}</div>
    @endif
    <div class="col-md-6">
        <ul class="list-group">
            <li class="list-group-item">Магазин: <strong>{{$table['vendor_name']}}</strong> </li>
            <li class="list-group-item">Все заказы: <strong>{{$table['total_orders_sum']}}</strong> </li>
            <li class="list-group-item">Выполненные заказы: <strong> {{$table['total_orders_sum_4_status']}}</strong></li>
            <li class="list-group-item">Системный налог: <strong>{{$table['total_tax']}}</strong> </li>
            <li class="list-group-item">Оплачено: <strong>{{ App\Models\VendorTax::where('vendor_id', $table['vendor_id'])->sum('amount') }}</strong> </li>
            <li class="list-group-item">Долг: <strong>{{$table['debt']}}</strong> </li>
          </ul>
    
    
        <form action="{{route('admin.statistics.store')}}" method="POST">
            @csrf
            <div class="form-group">
              <label for="exampleInputEmail1">Введите сумму</label>
              <input type="number" name="amount" class="form-control" id="exampleInputEmail1" placeholder="Введите сумму">
              <input type="hidden" name="vendor_id" value="{{$table['vendor_id']}}">
            </div>
          
          
           
            <button type="submit" class="btn btn-default">Сохранить</button>
          </form>
    







          <table class="table" style="margin-top:20px;"> 
            <caption>История транзакции</caption> 
            <thead> 
              <tr>
                 <th>#</th>
                 <th>Задолженность до выплаты</th>
                 <th>Сумма оплаты</th> 
                 <th>Задолженность после оплаты</th>
             </tr> 
            </thead> 
          <tbody> 
          @foreach(App\Models\VendorTax::where('vendor_id', $table['vendor_id'])->orderBy('created_at', 'desc')->get() as $item)
           <tr>
              <th scope="row">{{$loop->iteration}}</th>
              <td>{{$item->debt_before}}</td>
              <td>{{$item->amount}}</td>
              <td>{{$item->debt_after}}</td>
           </tr> 
           @endforeach
          </tbody> 
        </table>










    </div>


</div>


