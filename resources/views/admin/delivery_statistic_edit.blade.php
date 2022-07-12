
<div class="row">


    {{-- @if(session()->has('success'))
        <div >Транзакция прошел успешно</div>
    @endif --}}
    @if($errors->any())
    <div class="alert alert-success" role="alert">{{$errors->first()}}</div>
    @endif
    <div class="col-md-6">
        <ul class="list-group">
            <li class="list-group-item">Доставшик: <strong>{{$table['name']}}</strong> </li>
            <li class="list-group-item">Все заказы: <strong>{{$table['total_orders_sum']}}</strong> </li>
            <li class="list-group-item">Выполненные заказы: <strong> {{$table['total_orders_sum_4_status']}}</strong></li>
            <li class="list-group-item">Заработанная оплата за доставку: <strong>{{$table['total_fee']}}</strong> </li>
            <li class="list-group-item">Оплачено: <strong>{{$table['earnings']}}</strong> </li>
            <li class="list-group-item">Долг: <strong>{{$table['total_fee'] - $table['earnings']}}  </strong> </li>
          </ul>
    
    
        <form action="{{route('admin.delivery-statistics.store')}}" method="POST">
            @csrf
            <div class="form-group">
              <label for="exampleInputEmail1">Введите сумму</label>
              <input type="number" name="amount" class="form-control" id="exampleInputEmail1" placeholder="Введите сумму">
              <input type="hidden" name="id" value="{{$table['id']}}">
            </div>
            <button type="submit" class="btn btn-default">Сохранить</button>
          </form>
    







          <table class="table" style="margin-top:20px;"> 
            <caption>История транзакции</caption> 
            <thead> 
              <tr>
                 <th>#</th>
                 <th>Баланс до выплаты</th>
                 <th>Сумма оплаты</th> 
                 <th>Баланс после оплаты</th>
                 <th>Дата транзакции</th>
             </tr> 
            </thead> 
          <tbody>
          @foreach(App\Models\DeliveryEarning::where('delivery_id', $table['id'])->orderBy('created_at', 'desc')->get() as $item)
           <tr>
              <th scope="row">{{$loop->iteration}}</th>
              <td>{{$item->balance_before}}</td>
              <td>{{$item->amount}}</td>
              <td>{{$item->balance_after}}</td>
              <td>{{$item->created_at}}</td>
           </tr> 
          @endforeach
          </tbody> 
        </table>










    </div>


</div>


